<!-- Context Menu -->
<div id="context-menu" style="display: none; position: absolute; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); border-radius: 5px; z-index: 1000; min-width: 150px; overflow: hidden;">
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee;" onclick="editFile()" id="cm-edit"><i class="fas fa-edit"></i> Edit</li>
        <li style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee;" onclick="extractFile()" id="cm-extract"><i class="fas fa-box-open"></i> Extract</li>
        <li style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee; color: red;" onclick="deleteFile()" id="cm-delete"><i class="fas fa-trash"></i> Delete</li>
    </ul>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="custom-modal" style="display: none;">
    <div class="modal-content">
        <h3>Upload Files</h3>
        <div class="tabs" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <button onclick="setUploadType('file')" class="tab-btn active">File</button>
            <button onclick="setUploadType('folder')" class="tab-btn">Folder</button>
            <button onclick="setUploadType('zip')" class="tab-btn">Zip</button>
        </div>
        
        <form id="uploadForm" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" onsubmit="Hostoo.showLoader()">
            @csrf
            <input type="hidden" name="type" id="uploadType" value="file">
            <input type="hidden" name="upload_path" value="{{ $subPath }}">
            
            <div id="input-file" class="upload-input">
                <input type="file" name="files[]" multiple class="form-control">
            </div>
            
            <div id="input-folder" class="upload-input" style="display: none;">
                <input type="file" name="project_files[]" webkitdirectory directory multiple class="form-control">
            </div>

            <div id="input-zip" class="upload-input" style="display: none;">
                <input type="file" name="zip_file" accept=".zip" class="form-control">
            </div>

            <div style="margin-top: 1rem; text-align: right;">
                <button type="button" onclick="toggleUploadModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Editor Modal -->
<div id="editorModal" class="custom-modal" style="display: none;">
    <div class="modal-content" style="max-width: 900px; width: 95%; height: 80vh; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;">Edit File: <span id="editor-filename" style="color: var(--primary);"></span></h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <select id="editor-theme" onchange="editor.setTheme(this.value)" style="padding: 5px; border-radius: 5px; border: 1px solid #ddd; background: #fff;">
                    <option value="ace/theme/one_dark" selected>VS Code (One Dark)</option>
                    <option value="ace/theme/dracula">Dracula</option>
                    <option value="ace/theme/monokai">Monokai</option>
                    <option value="ace/theme/github">GitHub (Light)</option>
                </select>
                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleFullscreenEditor()" title="Toggle Fullscreen" style="padding: 5px 10px;">
                    <i class="fas fa-expand" id="fullscreen-icon"></i>
                </button>
            </div>
        </div>
        
        <!-- Ace Editor Container -->
        <div id="ace-editor" style="width: 100%; flex-grow: 1; border-radius: 5px; border: 1px solid #333; font-family: 'Consolas', 'Monaco', 'Courier New', monospace; line-height: 1.5;"></div>

         <div style="margin-top: 1rem; text-align: right;">
            <button type="button" onclick="closeEditor()" class="btn btn-secondary">Close</button>
            <button type="button" onclick="saveFile()" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</div>

<style>
    /* Fullscreen Editor Mode */
    .modal-content.fullscreen {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100% !important;
        border-radius: 0 !important;
        padding: 1rem !important;
    }
    #ace-editor.fullscreen {
        border-radius: 0 !important;
        border-left: none;
        border-right: none;
    }
</style>

<!-- Ace Editor CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/ace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/ext-language_tools.min.js"></script>
<script>
    // Ensure Ace loads modes/themes from the correct CDN URL
    ace.config.set("basePath", "https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/");
</script>

<!-- Make Dir Modal -->
<div id="mkdirModal" class="custom-modal" style="display: none;">
    <div class="modal-content">
        <h3>New Folder</h3>
        <form action="{{ route('files.make-directory') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_path" value="{{ $subPath }}">
            <input type="text" name="name" class="form-control" placeholder="Folder Name" required>
            <div style="margin-top: 1rem; text-align: right;">
                <button type="button" onclick="toggleMkdirModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Make File Modal -->
<div id="mkfileModal" class="custom-modal" style="display: none;">
    <div class="modal-content">
        <h3>New File</h3>
        <form action="{{ route('files.create') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_path" value="{{ $subPath }}">
            <input type="text" name="name" class="form-control" placeholder="File Name (e.g. index.php)" required>
            <div style="margin-top: 1rem; text-align: right;">
                <button type="button" onclick="toggleMkfileModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .modal-content {
        background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .tab-btn {
        padding: 0.5rem 1rem; border: none; background: #ddd; border-radius: 5px; cursor: pointer;
    }
    .tab-btn.active {
        background: var(--primary); color: white;
    }
    #context-menu li:hover {
        background: #f8f9fa;
    }

    /* Toolbar Button Styles */
    .toolbar button, .toolbar .btn {
        border: none !important;
        outline: none !important;
        padding: 0.8rem 1.5rem; /* Uniform size */
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08); /* 3D Shadow */
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 45px; /* Fixed height for uniformity */
        color: white; /* Ensure text is white */
        margin: 0; /* consistent spacing */
    }

    .toolbar button:hover, .toolbar .btn:hover {
        transform: translateY(-2px); /* Lift up */
        box-shadow: 0 7px 14px rgba(0,0,0,0.15), 0 3px 6px rgba(0,0,0,0.1);
    }

    .toolbar button:active, .toolbar .btn:active {
        transform: translateY(1px); /* Press down */
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }

    /* Specific Colors if not handled by classes correctly or need overrides */
    .btn-secondary { background-color: #6c757d; }
    .btn-danger { background-color: #dc3545; }
    .btn-primary { background-color: var(--primary); }
    .btn-info { background-color:rgb(23, 162, 184); }
    .btn-success { background-color: #28a745; }

    /* Remove default outline focus */
    .toolbar button:focus, .toolbar .btn:focus {
        outline: none;
    }
</style>
