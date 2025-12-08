@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2>My Databases</h2>
        <p>Manage your MySQL databases and users.</p>
    </div>

    <!-- Main Content -->
    <div class="white-card">
        <!-- Toolbar -->
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h4 style="margin: 0; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 1.1rem; color: var(--primary);">
                <i class="fas fa-database"></i> 
                {{ count($databases) }} Databases
            </h4>

            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('createDbModal')">
                <i class="fas fa-plus" style="margin-right: 5px;"></i> Create New Database
            </button>
        </div>

        <!-- Database Grid/Table -->
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600; border-radius: 10px 0 0 10px;">Database Name</th>
                        <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600;">Username</th>
                        <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600;">Password</th>
                        <th style="padding: 1rem; text-align: left; color: #666; font-weight: 600;">Description</th>
                        <th style="padding: 1rem; text-align: right; color: #666; font-weight: 600; border-radius: 0 10px 10px 0;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($databases as $db)
                    <!-- Ondblclick to open phpMyAdmin -->
                    <tr class="file-item" ondblclick="window.open('/phpmyadmin', '_blank')" title="Double click to open phpMyAdmin">
                        <td style="padding: 1rem; font-weight: 500; border-bottom: 1px solid #eee;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: rgba(223, 105, 81, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-database"></i>
                                </div>
                                {{ $db->name }}
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid #eee;">
                            <span style="background: #f1f3f5; padding: 4px 10px; border-radius: 6px; font-size: 0.9em; color: #555;">
                                <i class="fas fa-user-circle" style="margin-right: 5px; color: #aaa;"></i>{{ $db->db_username ?? '-' }}
                            </span>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid #eee;" onclick="event.stopPropagation()">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="password" value="{{ $db->db_password }}" id="pass-{{ $db->id }}" readonly style="border:none; background:transparent; width: 80px; color: #888; font-family: monospace;" class="password-field">
                                <i class="fas fa-eye" onclick="togglePassword('{{ $db->id }}', this)" style="cursor: pointer; color: #ccc; transition: 0.2s;"></i>
                                <i class="fas fa-copy" onclick="copyPassword('{{ $db->id }}')" style="cursor: pointer; color: #ccc; transition: 0.2s;" title="Copy Password"></i>
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid #eee; color: #666;">
                            {{ \Illuminate\Support\Str::limit($db->description ?? '-', 40) }}
                        </td>
                        <td style="padding: 1rem; text-align: right; border-bottom: 1px solid #eee;" onclick="event.stopPropagation()">
                            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                <a href="/phpmyadmin" target="_blank" class="btn btn-warning btn-sm" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; background: #FFC107; border: none; color: #333 !important;">
                                    <i class="fas fa-database"></i> PMA
                                </a>
                                <button type="button" class="btn btn-info btn-sm" onclick="openImportModal('{{ $db->id }}', '{{ $db->name }}')" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 8px;">
                                    <i class="fas fa-file-import"></i> Import
                                </button>
                                <form action="{{ route('databases.destroy', $db->id) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 8px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem 1rem; color: #999;">
                            <div style="margin-bottom: 1rem; width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-database" style="font-size: 2.5rem; color: #e0e0e0;"></i>
                            </div>
                            <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">No databases found.</p>
                            <p style="font-size: 0.9rem;">Create your first database to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .file-item:hover {
        background-color: #fcfcfc !important;
    }
    .file-item:hover td {
        color: var(--primary);
    }
    body.dark-mode .file-item:hover {
        background-color: #2a2a2a !important;
    }
    body.dark-mode tr {
        border-bottom-color: #333 !important;
    }
    body.dark-mode th {
        background: #1e1e1e;
        color: #aaa !important;
    }
</style>
@endpush

<!-- Create Database Modal -->
<div id="createDbModal" class="custom-modal">
    <div class="modal-content">
        <h3>Create New Database</h3>
        <form action="{{ route('databases.store') }}" method="POST">
            @csrf
            
            <!-- Database Name -->
            <div class="form-group">
                <label>Database Name (Suffix)</label>
                <div class="d-flex align-items-center" style="display: flex; align-items: stretch;">
                    <span class="input-group-text" style="padding: 0.8rem; background: #f8f9fa; border: 1px solid #ddd; border-right: none; border-radius: 5px 0 0 5px; color: #666;">db_{{ Auth::id() }}_</span>
                    <input type="text" name="name" class="form-control" placeholder="shop_app" required style="border-radius: 0 5px 5px 0; border: 1px solid #ddd; border-left: none;">
                </div>
            </div>

            <!-- Database Username -->
            <div class="form-group">
                <label>Database User (Suffix)</label>
                <div class="d-flex align-items-center" style="display: flex; align-items: stretch;">
                    <span class="input-group-text" style="padding: 0.8rem; background: #f8f9fa; border: 1px solid #ddd; border-right: none; border-radius: 5px 0 0 5px; color: #666;">usr_{{ Auth::id() }}_</span>
                    <input type="text" name="username" class="form-control" placeholder="admin" required style="border-radius: 0 5px 5px 0; border: 1px solid #ddd; border-left: none;">
                </div>
            </div>
            
            <!-- Database Password -->
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="strongPassword123" required minlength="8">
            </div>

            <div class="form-group">
                <label>Description (Optional)</label>
                <input type="text" name="description" class="form-control" placeholder="E-commerce DB">
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createDbModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Database</button>
            </div>
        </form>
    </div>
</div>

<!-- Import SQL Modal -->
<div id="importModal" class="custom-modal">
    <div class="modal-content">
        <h3>Import SQL File</h3>
        <p style="color: #666; margin-bottom: 1.5rem;">Importing into: <strong id="import-db-name" style="color: var(--primary);"></strong></p>
        
        <form id="importForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Select .sql File</label>
                <input type="file" name="sql_file" class="form-control" accept=".sql,.txt" required>
                <small style="color: #888; margin-top: 5px; display: block;">Max size: 10MB</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('importModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Import SQL</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    function openImportModal(dbId, dbName) {
        document.getElementById('import-db-name').textContent = dbName;
        // Construct the route dynamically
        var form = document.getElementById('importForm');
        form.action = "/databases/" + dbId + "/import"; 
        openModal('importModal');
    }
    
    function togglePassword(id, icon) {
        var input = document.getElementById('pass-' + id);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function copyPassword(id) {
        var input = document.getElementById('pass-' + id);
        input.type = "text"; // temporarily show to select
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(input.value).then(function() {
            Hostoo.alert({title:'Copied', text:'Password copied to clipboard', type:'success'});
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
        input.type = "password"; // Re-hide
    }
    
    function confirmDelete(e, form) {
        e.preventDefault();
        Hostoo.alert({
            title: 'Confirm Delete',
            text: 'Are you sure? This will PERMANENTLY DELETE the database!',
            type: 'warning',
            showCancel: true
        }).then(confirmed => {
            if(confirmed) {
                form.submit();
            }
        });
        return false;
    }

    // Close on click outside
    window.onclick = function(event) {
        var createModal = document.getElementById('createDbModal');
        var importModal = document.getElementById('importModal');
        if (event.target == createModal) closeModal('createDbModal');
        if (event.target == importModal) closeModal('importModal');
    }
</script>

<style>
    /* Ensure Button Text is Visible */
    .btn-primary, .btn-secondary, .btn-danger, .btn-info {
        color: white !important;
    }
    
    .file-item {
        transition: background-color 0.2s ease;
    }
    .file-item:hover {
        background-color: #f8f9fa !important;
    }

    /* Modal Styles from modals.css should be loading, but ensuring here just in case */
    .custom-modal {
        display: none; /* Hidden by default */
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .custom-modal.show {
        display: flex;
        opacity: 1; pointer-events: auto; 
        animation: fadeIn 0.3s forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-content {
        background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
        transform: translateY(-20px);
        transition: transform 0.3s ease;
    }
    .custom-modal.show .modal-content { transform: translateY(0); }
    
    .modal-actions { margin-top: 2rem; text-align: right; display: flex; justify-content: flex-end; gap: 10px; }
    
    /* Toolbar Button Consistent Styles */
    .toolbar .btn {
        border: none !important;
        outline: none !important;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        transition: transform 0.1s;
    }
    .toolbar .btn:active { transform: scale(0.98); }
</style>
@endsection
