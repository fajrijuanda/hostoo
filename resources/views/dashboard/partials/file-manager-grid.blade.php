<div class="file-grid-container" style="background: white; padding: 2rem; border-radius: 20px; box-shadow: var(--shadow);">
    @if(count($files) > 0 || count($directories) > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #eee;">
                <th style="padding: 0.5rem; width: 40px;"><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                <th style="padding: 0.5rem; text-align: left;">Name</th>
                <th style="padding: 0.5rem; text-align: right;">Size</th>
                <th style="padding: 0.5rem; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Directories -->
            @foreach($directories as $dir)
            @php
                $prefix = 'uploads/' . Illuminate\Support\Facades\Auth::id() . '/';
                $folderName = basename($dir);
                $newPath = $subPath ? $subPath . '/' . $folderName : $folderName;
            @endphp
            <tr class="file-item" 
                onclick="toggleRowSelection(this, event)"
                ondblclick="window.location.href='{{ route('dashboard.files', ['path' => $newPath]) }}'" 
                style="cursor: pointer; user-select: none;">
                <td style="padding: 1rem;"><input type="checkbox" name="selected[]" value="{{ $dir }}" class="file-checkbox"></td>
                <td style="padding: 1rem;">
                    <span style="font-weight: 500; display: flex; align-items: center; gap: 10px; color: var(--secondary);">
                        <i class="fas fa-folder fa-lg" style="color: #ffc107;"></i>
                        {{ $folderName }}
                    </span>
                </td>
                <td style="padding: 1rem; text-align: right;">-</td>
                <td style="padding: 1rem; text-align: right;"></td>
            </tr>
            @endforeach

            <!-- Files -->
            @foreach($files as $file)
            @php 
                $fileSafe = str_replace('\\', '/', $file);
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $isZip = $ext === 'zip';
                $isCode = in_array($ext, ['php', 'html', 'css', 'js', 'txt', 'blade.php', 'json']);
            @endphp
            <tr class="file-item" data-path="{{ $fileSafe }}" data-type="{{ $ext }}" 
                onclick="toggleRowSelection(this, event)"
                ondblclick="editFile('{{ $fileSafe }}')" 
                oncontextmenu="showContextMenu(event, '{{ $fileSafe }}', '{{ $ext }}')"
                style="cursor: pointer; user-select: none;">
                <td style="padding: 1rem;"><input type="checkbox" name="selected[]" value="{{ $fileSafe }}" class="file-checkbox"></td>
                <td style="padding: 1rem;">
                    <span style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas {{ $isZip ? 'fa-file-archive' : ($isCode ? 'fa-file-code' : 'fa-file') }} fa-lg" style="color: #777BB4;"></i>
                        {{ basename($file) }}
                    </span>
                </td>
                <td style="padding: 1rem; text-align: right;">{{ number_format(Storage::disk('public')->size($file) / 1024, 2) }} KB</td>
                <td style="padding: 1rem; text-align: right;">
                     <div style="position: relative; display: inline-block;">
                        <i class="fas fa-ellipsis-v" style="cursor: pointer; padding: 0 10px;" onclick="showContextMenu(event, '{{ $fileSafe }}', '{{ $ext }}'); event.stopPropagation();"></i>
                     </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div style="text-align: center; padding: 3rem; color: #999;">
            <i class="fas fa-folder-open fa-3x" style="margin-bottom: 1rem; display: block;"></i>
            No files found. Upload something to get started!
        </div>
    @endif
</div>
