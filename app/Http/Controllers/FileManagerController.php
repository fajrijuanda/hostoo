<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FileManagerController extends Controller
{
    private function getDisk()
    {
        $domain = Auth::user()->domains()->first();

        if ($domain && $domain->ftp_host && $domain->ftp_username) {
            $config = [
                'driver' => 'ftp',
                'host' => $domain->ftp_host,
                'username' => $domain->ftp_username,
                'password' => $domain->ftp_password, // Decrypted automatically by cast
                'root' => '/public_html', // Default for CyberPanel
                'port' => 21, 
                // 'ssl' => true, // Optional: if CyberPanel supports explicit SSL
            ];
            return Storage::build($config);
        }

        // Fallback (or shouldn't happen due to index check)
        return Storage::disk('public');
    }

    private function getUserDirectory()
    {
        // For FTP, we are at root of the FTP account usually
        return ''; 
    }

    public function index(Request $request)
    {
        if (!Auth::user()->domains()->exists()) {
            return redirect()->route('domains.index')->with('error', 'Please connect a domain before accessing File Manager.');
        }

        $disk = $this->getDisk();
        $subPath = $request->query('path', '');

        // Security
        if (str_contains($subPath, '..')) abort(403);

        $currentPath = $subPath; // On FTP root is valid

        try {
            if (!$disk->exists($currentPath)) {
                return redirect()->route('dashboard.files')->with('error', 'Directory not found.');
            }

            $files = $disk->files($currentPath);
            $directories = $disk->directories($currentPath);
        } catch (\Exception $e) {
            return redirect()->route('dashboard.files')->with('error', 'Failed to connect to Remote Storage (FTP). Please check your domain status.');
        }

        sort($directories);
        sort($files);

        return view('dashboard.file_manager', compact('files', 'directories', 'subPath'));
    }

    public function upload(Request $request)
    {
        $disk = $this->getDisk();
        $type = $request->input('type');
        $uploadPath = $request->input('upload_path', '');
        
        if (str_contains($uploadPath, '..')) abort(403);
        
        $directory = $uploadPath; // Relative to FTP root

        if ($type === 'file' && $request->hasFile('files')) {
            $request->validate(['files.*' => 'file|max:51200']); // 50MB
            foreach ($request->file('files') as $file) {
                 $filename = $file->getClientOriginalName();
                 $disk->putFileAs($directory, $file, $filename);
            }
            return back()->with('success', 'Files uploaded to remote server.');
        } 
        elseif ($type === 'folder' && $request->hasFile('project_files')) {
             foreach ($request->file('project_files') as $file) {
                // Determine relative path - specialized handling needed for folder structure
                // For simplicity, flattening or using client path if available.
                // Standard PHP file upload structure doesn't easily preserve folder structure 
                // without webkitRelativePath support in a specific way.
                // Assuming simple upload for now.
                $disk->putFileAs($directory, $file, $file->getClientOriginalName());
            }
             return back()->with('success', 'Folder uploaded.');
        }

        return back()->with('error', 'Upload failed.');
    }

    public function extract(Request $request)
    {
        return back()->with('error', 'Zip extraction is not supported on Remote Storage.');
    }

    public function compress(Request $request)
    {
        $request->validate(['files' => 'required|array']);
        $disk = $this->getDisk();
        $files = $request->input('files');
        $archiveName = $request->input('archive_name', 'archive') . '.zip';
        
        // Use a unique temp file
        $tempPath = tempnam(sys_get_temp_dir(), 'zip');
        $zip = new ZipArchive;

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                // $file is the relative path on the disk
                $basename = basename($file);
                
                if ($disk->exists($file)) {
                    // Check if it's a directory (naive check or based on trailing slash if provided, but Storage doesn't always distinguish easily without metadata)
                    // For simplicity in this 'Manager', we'll treat everything as file content unless we do recursive.
                    // To properly handle folders, we'd need to iterate.
                    // Let's assume File Selection for now. 
                    
                    if ($disk->getMetadata($file)['type'] === 'dir') {
                         // Recursive add directory
                         $this->addFolderToZip($disk, $file, $zip, $basename);
                    } else {
                         $content = $disk->get($file);
                         $zip->addFromString($basename, $content);
                    }
                }
            }
            $zip->close();
            
            // Upload the zip back to the current directory (parent of the first file or current path?)
            // The file list might be in subfolder.
            // We should put it in the same folder as the files.
            // Let's assume files are in same dir.
            $directory = dirname($files[0]);
            if ($directory == '.') $directory = '';
            
            $targetPath = ($directory ? $directory . '/' : '') . $archiveName;
            
            // Ensure unique name
            if ($disk->exists($targetPath)) {
                $targetPath = ($directory ? $directory . '/' : '') . time() . '_' . $archiveName;
            }

            $disk->put($targetPath, file_get_contents($tempPath));
            unlink($tempPath);
            
            return back()->with('success', 'Archive created successfully.');
        }

        return back()->with('error', 'Failed to create archive.');
    }

    private function addFolderToZip($disk, $folderPath, $zip, $zipPath) {
        $zip->addEmptyDir($zipPath);
        $files = $disk->files($folderPath);
        $directories = $disk->directories($folderPath);

        foreach ($files as $file) {
            $zip->addFromString($zipPath . '/' . basename($file), $disk->get($file));
        }
        foreach ($directories as $dir) {
            $this->addFolderToZip($disk, $dir, $zip, $zipPath . '/' . basename($dir));
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['files' => 'required|array']);
        $disk = $this->getDisk();
        $count = 0;

        foreach ($request->input('files') as $file) {
            // $file comes as 'path/filename' relative to user root usually?
            // In the view, the checkbox value should be the relative path.
             
             // Since we use the same view, we assume $file is relative path.
             if ($disk->exists($file)) {
                 try {
                     // Try deleting as file first
                     if ($disk->delete($file)) {
                         $count++;
                     } 
                     // Or directory
                     elseif ($disk->deleteDirectory($file)) {
                         $count++;
                     }
                 } catch (\Exception $e) { /* ignore */ }
             }
        }
        
        return back()->with('success', "Deleted $count items.");
    }
    
    public function makeDirectory(Request $request) {
        $request->validate(['name' => 'required|string|alpha_dash']);
        $disk = $this->getDisk();
        $parentPath = $request->input('parent_path', '');
        
        if (str_contains($parentPath, '..')) abort(403);
        
        $path = ($parentPath ? $parentPath . '/' : '') . $request->name;
        
        if ($disk->makeDirectory($path)) {
             return back()->with('success', 'Directory created.');
        }
        return back()->with('error', 'Failed to create directory.');
    }

    public function createFile(Request $request) {
        $request->validate(['name' => 'required|string']);
        $disk = $this->getDisk();
        $parentPath = $request->input('parent_path', '');
        $path = ($parentPath ? $parentPath . '/' : '') . $request->name;

        if ($disk->exists($path)) {
            return back()->with('error', 'File already exists.');
        }

        if ($disk->put($path, '')) {
             return back()->with('success', 'File created successfully.');
        }
        return back()->with('error', 'Failed to create file.');
    }

    public function getContent(Request $request) {
        $path = $request->query('path');
        $disk = $this->getDisk();
        
        if (!$disk->exists($path)) {
             return response()->json(['error' => 'File not found'], 404);
        }

        $content = $disk->get($path);
        return response()->json(['content' => $content]);
    }

    public function updateContent(Request $request) {
        $path = $request->input('path');
        $content = $request->input('content');
        $disk = $this->getDisk();

        $disk->put($path, $content);
        return response()->json(['success' => true]);
    }
}
