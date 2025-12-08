<script>
    let currentPath = '';

    // Context Menu Logic
    function showContextMenu(e, path, type) {
        e.preventDefault();
        currentPath = path;
        
        const menu = document.getElementById('context-menu');
        menu.style.display = 'block';
        menu.style.left = e.pageX + 'px';
        menu.style.top = e.pageY + 'px';

        // Toggle Items based on type
        document.getElementById('cm-extract').style.display = type === 'zip' ? 'block' : 'none';
        document.getElementById('cm-edit').style.display = ['php','html','css','js','txt'].includes(type) ? 'block' : 'none';
    }

    // Hide context menu on click elsewhere
    document.addEventListener('click', function(e) {
        document.getElementById('context-menu').style.display = 'none';
    });

    // Ace Editor Init
    let editor;
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('ace-editor')) {
            ace.require("ace/ext/language_tools");
            editor = ace.edit("ace-editor");
            // Set default theme to One Dark (VS Code-like) or Dracula
            editor.setTheme("ace/theme/one_dark");
            
            editor.session.setMode("ace/mode/php");
            editor.setOptions({
                fontSize: "15px", // Larger font
                fontFamily: "'Fira Code', 'Consolas', 'Menlo', monospace",
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: true,
                showPrintMargin: false,
                wrap: true, // Enable word wrap
                highlightActiveLine: true,
                highlightGutterLine: true,
            });
            
            // Fix for dynamic resizing if needed
            new ResizeObserver(() => editor.resize()).observe(document.getElementById('ace-editor'));
        }
    });

    // Editor Logic
    function editFile(path = null) {
        if (path) currentPath = path;
        
        const modal = document.getElementById('editorModal');
        const filenameSpan = document.getElementById('editor-filename');
        const filename = currentPath.split('/').pop();

        filenameSpan.textContent = filename;
        
        // Detect Mode
        const ext = filename.split('.').pop().toLowerCase();
        let mode = 'ace/mode/text';
        const modes = {
            'php': 'php', 'html': 'html', 'css': 'css', 'js': 'javascript', 'json': 'json',
            'xml': 'xml', 'sql': 'sql', 'blade.php': 'php', 'md': 'markdown'
        };
        if (modes[ext]) mode = 'ace/mode/' + modes[ext];
        if (filename.includes('blade')) mode = 'ace/mode/php'; // Blade workaround

        editor.session.setMode(mode);

        // Fetch Content
        Hostoo.showLoader();
        fetch(`{{ route('files.content') }}?path=${encodeURIComponent(currentPath)}`)
            .then(res => res.json())
            .then(data => {
                Hostoo.hideLoader();
                editor.setValue(data.content, -1); // -1 moves cursor to start
                modal.style.display = 'flex';
                editor.resize();
            })
            .catch(() => Hostoo.hideLoader());
    }

    function saveFile() {
        const content = editor.getValue();
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        Hostoo.showLoader();
        fetch(`{{ route('files.save') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ path: currentPath, content: content })
        }).then(res => res.json()).then(data => {
            Hostoo.hideLoader();
            if(data.success) {
                // simple toast or alert
                const btn = document.querySelector('#editorModal .btn-primary');
                const originalText = btn.innerText;
                btn.innerText = 'Saved!';
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.classList.remove('btn-success');
                    closeEditor();
                }, 800);
            } else {
                Hostoo.alert({title:'Error', text:'Failed to save file.', type:'error'});
            }
        });
    }

    function closeEditor() {
        document.getElementById('editorModal').style.display = 'none';
        // Reset fullscreen on close
        const modalContent = document.querySelector('#editorModal .modal-content');
        if(modalContent.classList.contains('fullscreen')) {
            toggleFullscreenEditor();
        }
    }

    function toggleFullscreenEditor() {
        const modalContent = document.querySelector('#editorModal .modal-content');
        const icon = document.getElementById('fullscreen-icon');
        
        modalContent.classList.toggle('fullscreen');
        
        if (modalContent.classList.contains('fullscreen')) {
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
        
        // Force resize ace editor to fill new space
        setTimeout(() => {
            editor.resize();
        }, 100);
    }

    // Extract Logic
    function extractFile() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("files.extract") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const pathInput = document.createElement('input');
        pathInput.type = 'hidden';
        pathInput.name = 'path';
        pathInput.value = currentPath;

        form.appendChild(csrf);
        form.appendChild(pathInput);
        document.body.appendChild(form);
        Hostoo.showLoader();
        form.submit();
    }

    // Delete Logic
    function deleteFile() {
        Hostoo.alert({title:'Confirm Delete', text:'Are you sure you want to delete this file?', type:'warning', showCancel:true})
        .then(function(confirmed) {
            if(confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("files.delete") }}';
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const pathInput = document.createElement('input');
                pathInput.type = 'hidden';
                pathInput.name = 'files[]';
                pathInput.value = currentPath;

                form.appendChild(csrf);
                form.appendChild(pathInput);
                document.body.appendChild(form);
                Hostoo.showLoader();
                form.submit();
            }
        });
    }

    // Modal Toggles
    function toggleUploadModal() {
        const el = document.getElementById('uploadModal');
        el.style.display = el.style.display === 'flex' ? 'none' : 'flex';
    }
    function toggleMkdirModal() {
        const el = document.getElementById('mkdirModal');
        el.style.display = el.style.display === 'flex' ? 'none' : 'flex';
    }
    function toggleMkfileModal() {
        const el = document.getElementById('mkfileModal');
        el.style.display = el.style.display === 'flex' ? 'none' : 'flex';
    }

    function setUploadType(type) {
        document.getElementById('uploadType').value = type;
        document.querySelectorAll('.upload-input').forEach(el => el.style.display = 'none');
        document.getElementById('input-' + type).style.display = 'block';
        
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
        event.preventDefault(); // Prevent form submit
    }

    // Selection Logic
    function toggleRowSelection(row, event) {
        // Prevent triggering if clicked on checkbox itself (handled natively)
        if (event.target.type === 'checkbox') {
            updateRowStyle(row);
            updateBulkActions();
            return;
        }

        const checkbox = row.querySelector('.file-checkbox');
        checkbox.checked = !checkbox.checked;
        updateRowStyle(row);
        updateBulkActions();
    }

    function updateRowStyle(row) {
        const checkbox = row.querySelector('.file-checkbox');
        if (checkbox.checked) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    }
    
    // Override toggleSelectAll to update styles
    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.file-checkbox');
        const master = document.getElementById('select-all');
        const rows = document.querySelectorAll('.file-item');
        
        checkboxes.forEach(cb => cb.checked = master.checked);
        rows.forEach(row => updateRowStyle(row));
        updateBulkActions();
    }

    function confirmBulkDelete(e) {
        if(e) e.preventDefault();
        Hostoo.alert({
            title: 'Confirm Bulk Delete',
            text: 'Are you sure you want to delete the selected items?',
            type: 'warning',
            showCancel: true
        }).then(confirmed => {
            if(confirmed) {
                Hostoo.showLoader();
                document.getElementById('bulk-delete-form').submit();
            }
        });
    }

    function updateBulkActions() {
        const selected = document.querySelectorAll('.file-checkbox:checked');
        const deleteForm = document.getElementById('bulk-delete-form');
        const zipForm = document.getElementById('bulk-zip-form');
        
        if (selected.length > 0) {
            deleteForm.style.display = 'inline-block';
            zipForm.style.display = 'inline-block';
            
            // Populate hidden inputs
            const deleteContainer = document.getElementById('bulk-delete-inputs');
            deleteContainer.innerHTML = '';
             const zipContainer = document.getElementById('bulk-zip-inputs');
            zipContainer.innerHTML = '';

            selected.forEach(cb => {
                // Determine name based on controller expectation (sending both to be safe)
                const inputF = document.createElement('input');
                inputF.type = 'hidden';
                inputF.name = 'files[]';
                inputF.value = cb.value;
                deleteContainer.appendChild(inputF);

                const inputS = document.createElement('input');
                inputS.type = 'hidden';
                inputS.name = 'selected[]';
                inputS.value = cb.value;
                deleteContainer.appendChild(inputS);
                
                // For Zip
                const inputZip = document.createElement('input');
                inputZip.type = 'hidden';
                inputZip.name = 'files[]';
                inputZip.value = cb.value;
                zipContainer.appendChild(inputZip);
            });
        } else {
            deleteForm.style.display = 'none';
            zipForm.style.display = 'none';
        }
    }

    // Initialize Bulk Actions
    document.addEventListener('DOMContentLoaded', () => {
        updateBulkActions();
    });
</script>

<style>
    /* Selection Styles */
    .file-item {
        transition: background-color 0.1s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .file-item:hover {
        background-color: #f8f9fa;
    }
    .file-item.selected {
        background-color: #f1f3f5 !important; /* Light Gray */
    }
    
    /* Custom Checkbox Orange Accent */
    .file-checkbox {
        accent-color: var(--accent); /* Orange */
        cursor: pointer;
        width: 16px; 
        height: 16px;
    }
</style>
