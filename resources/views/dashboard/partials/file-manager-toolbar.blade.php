<div class="toolbar" style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: var(--shadow); display: flex; gap: 1rem; align-items: center;">
    
    <!-- Breadcrumbs -->
    <h4 style="margin: 0; margin-right: auto; display: flex; align-items: center; gap: 5px; font-weight: 500;">
        <a href="{{ route('dashboard.files') }}" style="text-decoration: none; color: var(--primary);">
            <i class="fas fa-home"></i> My Files
        </a>
        @if($subPath)
            @php
                $crumbs = explode('/', $subPath);
                $accumulated = '';
            @endphp
            @foreach($crumbs as $crumb)
                @php $accumulated .= ($accumulated ? '/' : '') . $crumb; @endphp
                <span style="color: #ccc;">/</span>
                <a href="{{ route('dashboard.files', ['path' => $accumulated]) }}" style="text-decoration: none; color: {{ $loop->last ? '#333' : 'var(--primary)' }}; font-weight: {{ $loop->last ? 'bold' : 'normal' }}">
                    {{ $crumb }}
                </a>
            @endforeach
        @endif
    </h4>
    
    <!-- Bulk Actions -->
    <form action="{{ route('files.delete') }}" method="POST" id="bulk-delete-form" style="display: none;">
        @csrf
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete(event)">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
        <div id="bulk-delete-inputs"></div>
    </form>

    <form action="{{ route('files.compress') }}" method="POST" id="bulk-zip-form" style="display: none;">
        @csrf
        <input type="hidden" name="archive_name" value="archive">
        <button type="submit" class="btn btn-secondary btn-sm">
            <i class="fas fa-file-archive"></i> Zip Selected
        </button>
        <div id="bulk-zip-inputs"></div>
    </form>

    <button onclick="toggleUploadModal()" class="btn btn-primary btn-sm">
        <i class="fas fa-upload"></i> Upload
    </button>
    <button onclick="toggleMkdirModal()" class="btn btn-info btn-sm" style="color: white; background-color: #17a2b8;">
        <i class="fas fa-folder-plus"></i> New Folder
    </button>
    <button onclick="toggleMkfileModal()" class="btn btn-success btn-sm" style="color: white; background-color: #28a745;">
        <i class="fas fa-file-plus"></i> New File
    </button>
</div>
