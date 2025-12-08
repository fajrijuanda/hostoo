<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DatabaseController extends Controller
{
    protected $cyberPanel;

    public function __construct(\App\Services\CyberPanelService $cyberPanel)
    {
        $this->cyberPanel = $cyberPanel;
    }

    public function index()
    {
        $databases = UserDatabase::where('user_id', Auth::id())->get();
        return view('databases.index', compact('databases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|alpha_dash|max:64',
            'username' => 'required|string|alpha_dash|max:16',
            'password' => 'required|string|min:8',
            'description' => 'nullable|string|max:255',
        ]);

        // Get User's Website (Domain)
        $domain = Auth::user()->domains()->where('status', 'active')->first();
        if (!$domain) {
            return back()->withErrors(['domain' => 'You must have an active domain/website to create a database.']);
        }

        $websiteName = $domain->domain_name;
        $dbName = 'db_' . Auth::id() . '_' . $request->name;
        $dbUser = 'usr_' . Auth::id() . '_' . $request->username;
        $dbPass = $request->password;

        // Check local existence first (optional, but good for UX)
        if (UserDatabase::where('name', $dbName)->exists()) {
             return back()->withErrors(['name' => 'Database name already exists.']);
        }

        try {
            // Call CyberPanel API
            $result = $this->cyberPanel->createDatabase($websiteName, $dbName, $dbUser, $dbPass);

            // Check API result
            if (isset($result['error_message']) && $result['error_message'] != 'None') {
                 // Even if 'createDatabase' returns 1, sometimes it has error_message.
                 // Ideally check result status. CyberPanel returns { "createDatabase": 1, "error_message": "None" } on success.
                 if ($result['createDatabase'] === 0) {
                     throw new \Exception("CyberPanel Error: " . ($result['error_message'] ?? 'Unknown Error'));
                 }
            }

            // Save to Local DB
            UserDatabase::create([
                'user_id' => Auth::id(),
                'name' => $dbName,
                'db_username' => $dbUser,
                'db_password' => $dbPass,
                'description' => $request->description,
            ]);

            return redirect()->route('databases.index')->with('success', "Database $dbName created successfully on $websiteName!");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create database: ' . $e->getMessage());
        }
    }

    public function destroy(UserDatabase $database)
    {
        if ($database->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Call CyberPanel API
            $this->cyberPanel->deleteDatabase($database->name);

            // Local Delete
            $database->delete();

            return redirect()->route('databases.index')->with('success', 'Database deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete database: ' . $e->getMessage());
        }
    }

    public function import(UserDatabase $database)
    {
        // View not needed if using modal, but keeping it as fallback or error handler logic
        if ($database->user_id !== Auth::id()) {
            abort(403);
        }
        return view('databases.import', compact('database'));
    }

    public function processImport(Request $request, UserDatabase $database)
    {
        if ($database->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:10240', // 10MB limit
        ]);

        $file = $request->file('sql_file');
        $sql = file_get_contents($file->getRealPath());

        try {
            // Force use of the specific database for import
            $importSql = "USE `$database->name`; \n" . $sql;
            
            DB::connection('mysql')->unprepared($importSql);

            return redirect()->route('databases.index')->with('success', 'SQL file imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
