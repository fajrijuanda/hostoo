@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; width: 100%;">
        <div>
            <h1 style="color: var(--secondary); font-weight: 700; margin-bottom: 0.5rem; font-size: 2rem;">Hosting Plans</h1>
            <p style="color: var(--text-color); margin: 0;">Manage your hosting packages and pricing strategies.</p>
        </div>
        <button onclick="openCreateModal()" class="btn-primary" style="display: flex; align-items: center; gap: 8px; white-space: nowrap;">
            <i class="fas fa-plus"></i> Add New Plan
        </button>
    </div>

    <!-- Modified Grid to Flexbox for Left Alignment -->
    @if($plans->count() <= 4)
    <!-- Grid View for few items -->
    <div class="plans-grid" style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: flex-start;">
        @foreach($plans as $plan)
        @include('admin.plans.partials.plan-card', ['plan' => $plan])
        @endforeach
    </div>
    @else
    <!-- 3D Carousel View for many items -->
    <div class="carousel-scene" style="perspective: 1000px; width: 100%; height: 550px; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative;">
        <!-- Container for cards -->
        <div class="carousel-track" id="planCarousel" style="width: 100%; height: 100%; position: relative; transform-style: preserve-3d;">
            @foreach($plans as $index => $plan)
                <div class="carousel-cell" data-index="{{ $index }}" style="position: absolute; width: 280px; height: 420px; left: 50%; top: 50%; margin-left: -140px; margin-top: -210px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: box-shadow 0.3s;">
                    <!-- Card Content -->
                    <div class="plan-card" style="background: white; border-radius: 25px; overflow: hidden; width: 100%; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative; backface-visibility: hidden; display: flex; flex-direction: column;">
                         <!-- Plan Image -->
                        <div class="plan-image" style="height: 200px; width: calc(100% - 3rem); margin: 1.5rem; border-radius: 8px; background: url('{{ asset('storage/plans/' . $plan->image) }}') center/cover; position: relative; flex-shrink: 0;">
                             <div style="position: absolute; top: 10px; right: 10px; display: flex; gap: 5px; z-index: 10;">
                                <button onclick="openEditModal({{ json_encode($plan) }})" style="background: rgba(255,255,255,0.9); border:none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--primary);">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form id="delete-form-c-{{ $plan->id }}" action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="confirmDelete(event, 'delete-form-c-{{ $plan->id }}')" style="display: inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: rgba(255,255,255,0.9); border:none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #df6951;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="plan-content" style="padding: 0 1.5rem 1.5rem 1.5rem; text-align: left;">
                            <div class="plan-price" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; color: var(--text-color);">
                                <span style="font-weight: 700; color: var(--secondary); font-size: 1.1rem;">{{ $plan->name }}</span>
                                <span style="font-weight: 700; color: var(--primary); font-size: 1rem;">Rp {{ $plan->price >= 1000 ? round($plan->price / 1000) . 'K' : number_format($plan->price, 0, ',', '.') }}</span>
                            </div>
                            <p style="margin-bottom: 10px; font-size: 0.85rem; color: #666; font-style: italic;">{{ Str::limit($plan->description, 50) }}</p>
                            @if($plan->features)
                                <div style="font-size: 0.8rem; color: #555; max-height: 150px; overflow-y: auto; padding-right: 5px;">
                                    @foreach($plan->features as $feature)
                                        <div style="margin-bottom: 4px;">
                                            <i class="fas fa-check" style="margin-right: 5px; color: var(--primary);"></i> {{ $feature }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('planCarousel');
            if(!track) return;
            
            const cells = Array.from(track.querySelectorAll('.carousel-cell'));
            const count = cells.length;
            
            // Configuration
            const cardWidth = 300; 
            const visibleCount = 4;
            const gap = 320;
            const totalFrontWidth = gap * (visibleCount - 1);
            const leftLimit = -totalFrontWidth / 2;
            
            // State
            let targetProgress = 0;
            let isDragging = false;
            let startDragX = 0;
            let startDragProgress = 0;

            function getTransform(p) {
                // Normalize p to 0..count
                let norm = p % count;
                if (norm < 0) norm += count;
                
                // Logic: 0..4 (Front), 4..N (Back)
                const frontCount = 4;
                
                let x = 0, z = 0, rY = 0, zIndex = 0, opacity = 1;
                
                if (norm < frontCount) {
                    // FRONT LINEAR
                    x = leftLimit + (norm * gap);
                    z = 0;
                    rY = 0;
                    zIndex = 100;
                    opacity = 1;
                } 
                else if (norm >= frontCount && norm < frontCount + 0.5) {
                    // RIGHT END FLIP
                    let t = (norm - frontCount) / 0.5;
                    x = leftLimit + ((frontCount-1) * gap) + (t * 50); 
                    z = -200 * t;
                    rY = 180 * t;
                    zIndex = 50;
                    opacity = 1 - (t * 0.2);
                }
                else if (norm >= frontCount + 0.5 && norm < count - 0.5) {
                    // BACK RETURN
                    let backRange = count - 1 - frontCount;
                    if(backRange <= 0) backRange = 1; // safety
                    
                    let rangePos = norm - (frontCount + 0.5);
                    let t = rangePos / backRange;
                    
                    let rightEdge = leftLimit + ((frontCount-1) * gap) + 50;
                    let leftEdge = leftLimit - 50;
                    
                    x = rightEdge - (t * (rightEdge - leftEdge));
                    z = -200;
                    rY = 180;
                    zIndex = 0;
                    opacity = 0.8;
                }
                else {
                    // LEFT END FLIP
                    let t = (norm - (count - 0.5)) / 0.5;
                    x = (leftLimit - 50) + (t * 50);
                    z = -200 * (1-t);
                    rY = 180 * (1-t);
                    zIndex = 50;
                    opacity = 0.8 + (t * 0.2);
                }
                
                return `translateX(${x}px) translateZ(${z}px) rotateY(${rY}deg)`;
            }

            function update() {
                let p = targetProgress;
                
                cells.forEach((cell, i) => {
                    // Calc relative position
                    let pos = i - p;
                    while(pos < 0) pos += count;
                    while(pos >= count) pos -= count;
                    
                    cell.style.transform = getTransform(pos);
                    
                    // Simple z-index fix
                    let z = parseFloat(cell.style.transform.match(/translateZ\((-?\d+)/)?.[1] || 0);
                    cell.style.zIndex = z > -50 ? 100 : 10;
                });
                
                requestAnimationFrame(update);
            }
            
            update();

            // Interaction
            const scene = document.querySelector('.carousel-scene');
            
            function handleStart(x) {
                isDragging = true;
                startDragX = x;
                startDragProgress = targetProgress;
                scene.style.cursor = 'grabbing';
            }
            
            function handleMove(x) {
                if(!isDragging) return;
                const diff = x - startDragX;
                // Sensivitity: 300px = 1 item
                targetProgress = startDragProgress - (diff / 300);
            }
            
            function handleEnd() {
                isDragging = false;
                scene.style.cursor = 'default';
                // Snap to nearest integer
                targetProgress = Math.round(targetProgress);
            }

            scene.addEventListener('mousedown', e => handleStart(e.pageX));
            window.addEventListener('mousemove', e => handleMove(e.pageX));
            window.addEventListener('mouseup', handleEnd);
            
            scene.addEventListener('touchstart', e => handleStart(e.touches[0].clientX));
            window.addEventListener('touchmove', e => handleMove(e.touches[0].clientX));
            window.addEventListener('touchend', handleEnd);
            
        });
    </script>
    @endif
</div>

<!-- Define Plan Card Partial for DRY if needed, but for now just inline in carousel above was easier for custom styling -->
@if($plans->count() <= 4)
    {{-- We need the content for the grid loop since I removed it main block --}}
    {{-- Re-adding the loop logic inline here is cleaner for single file edit --}}
@endif
</div>

<!-- Create Modal -->
<div id="createModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 2.5rem; border-radius: 20px; width: 95%; max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="margin: 0; font-weight: 700; color: var(--secondary); font-size: 1.5rem;">Add New Plan</h3>
            <button onclick="closeModal('createModal')" style="background: none; border: none; font-size: 2rem; cursor: pointer; color: #999; line-height: 1;">&times;</button>
        </div>
        <form id="createForm" action="{{ route('admin.plans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2.5rem; align-items: start;">
                <!-- Left Column -->
                <div>
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary); margin-bottom: 8px; display: block;">Plan Image</label>
                        <div style="background: #f8f9fa; border: 2px dashed #e9ecef; border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer; position: relative;">
                            <img id="create_image_preview" src="" style="width: 100%; height: auto; max-height: 150px; object-fit: contain; margin-bottom: 1rem; display: none; border-radius: 8px;">
                            <input type="file" name="image" class="form-control" accept="image/*" onchange="handleImagePreview(this, 'create_image_preview')" style="width: 100%;">
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Plan Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Starter Plan" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Price (Rp)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;">
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                     <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Brief description of the plan..." style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Features (One per line)</label>
                        <textarea name="features_input" rows="5" class="form-control" placeholder="10GB Storage&#10;Free SSL&#10;Unlimited Bandwidth" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;"></textarea>
                    </div>

                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; border: 1px solid #dee2e6;">
                        <h5 class="mb-3" style="font-size: 1rem; color: var(--secondary); font-weight: 700; margin-top: 0;">Discount Settings (Optional)</h5>
                        <div class="form-group mb-3">
                             <label class="form-label" style="font-size: 0.9rem;">Discount Price (Rp)</label>
                             <input type="number" step="0.01" name="discount_price" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                        </div>
                        <div class="row" style="display: flex; gap: 15px;">
                            <div class="col" style="flex: 1;">
                                <label class="form-label" style="font-size: 0.9rem;">Start Date</label>
                                <input type="datetime-local" name="discount_start_date" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                            </div>
                            <div class="col" style="flex: 1;">
                                <label class="form-label" style="font-size: 0.9rem;">End Date</label>
                                <input type="datetime-local" name="discount_end_date" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-right" style="text-align: right; padding-top: 1rem; border-top: 1px solid #eee;">
                <button type="button" onclick="closeModal('createModal')" class="btn" style="padding: 12px 24px; border: none; background: #f1f3f5; color: #495057; border-radius: 10px; margin-right: 12px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 12px 30px; border: none; background: var(--primary); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(241, 165, 1, 0.3);">Create Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 2.5rem; border-radius: 20px; width: 95%; max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="margin: 0; font-weight: 700; color: var(--secondary); font-size: 1.5rem;">Edit Plan</h3>
            <button onclick="closeModal('editModal')" style="background: none; border: none; font-size: 2rem; cursor: pointer; color: #999; line-height: 1;">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2.5rem; align-items: start;">
                 <!-- Left Column -->
                 <div>
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary); margin-bottom: 8px; display: block;">Plan Image</label>
                        <div style="background: #f8f9fa; border: 2px dashed #e9ecef; border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer; position: relative;">
                            <img id="edit_image_preview" src="" style="width: 100%; height: auto; max-height: 150px; object-fit: contain; margin-bottom: 1rem; display: none; border-radius: 8px;">
                            <p style="font-size: 0.8rem; color: #999; margin-bottom: 10px;">Leave empty to keep current</p>
                            <input type="file" name="image" class="form-control" accept="image/*" onchange="handleImagePreview(this, 'edit_image_preview')" style="width: 100%;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Plan Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Price (Rp)</label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;">
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Description</label>
                        <textarea name="description" id="edit_description" rows="3" class="form-control" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" style="font-weight: 600; color: var(--secondary);">Features (One per line)</label>
                        <textarea name="features_input" id="edit_features_input" rows="5" class="form-control" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dee2e6; background: #f8f9fa;"></textarea>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; border: 1px solid #dee2e6;">
                        <h5 class="mb-3" style="font-size: 1rem; color: var(--secondary); font-weight: 700; margin-top: 0;">Discount Settings (Optional)</h5>
                        <div class="form-group mb-3">
                            <label class="form-label" style="font-size: 0.9rem;">Discount Price (Rp)</label>
                            <input type="number" step="0.01" name="discount_price" id="edit_discount_price" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                        </div>
                        <div class="row" style="display: flex; gap: 15px;">
                            <div class="col" style="flex: 1;">
                                <label class="form-label" style="font-size: 0.9rem;">Start Date</label>
                                <input type="datetime-local" name="discount_start_date" id="edit_discount_start_date" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                            </div>
                            <div class="col" style="flex: 1;">
                                <label class="form-label" style="font-size: 0.9rem;">End Date</label>
                                <input type="datetime-local" name="discount_end_date" id="edit_discount_end_date" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-right" style="text-align: right; padding-top: 1rem; border-top: 1px solid #eee;">
                <button type="button" onclick="closeModal('editModal')" class="btn" style="padding: 12px 24px; border: none; background: #f1f3f5; color: #495057; border-radius: 10px; margin-right: 12px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 12px 30px; border: none; background: var(--primary); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(241, 165, 1, 0.3);">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Hostoo.alert Wrapper for cleaner usage
    function confirmDelete(event, formId) {
        event.preventDefault();
        Hostoo.alert({
            title: 'Delete Plan?',
            text: 'Are you sure you want to remove this hosting plan? This cannot be undone.',
            type: 'warning',
            showCancel: true,
            confirmText: 'Yes, Delete'
        }).then((confirmed) => {
            if (confirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    // Modal Functions
    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
        // Reset preview
        const preview = document.getElementById('create_image_preview');
        preview.src = "{{ asset('images/placeholder-image.png') }}"; // Should ideally be a real placeholder or hidden
        preview.style.display = 'none';
        
        // Reset form
        document.getElementById('createForm').reset();
    }

    function openEditModal(plan) {
        document.getElementById('edit_name').value = plan.name;
        document.getElementById('edit_price').value = plan.price;
        document.getElementById('edit_description').value = plan.description || '';
        
        // Handle features array to string
        let featuresText = '';
        if (plan.features) {
            featuresText = plan.features.join('\n');
        }
        document.getElementById('edit_features_input').value = featuresText;

        document.getElementById('edit_discount_price').value = plan.discount_price || '';
        document.getElementById('edit_discount_start_date').value = plan.discount_start_date ? plan.discount_start_date.slice(0, 16) : '';
        document.getElementById('edit_discount_end_date').value = plan.discount_end_date ? plan.discount_end_date.slice(0, 16) : '';

        // Image Preview Logic
        const imagePreview = document.getElementById('edit_image_preview');
        if (plan.image) {
            imagePreview.src = "/storage/plans/" + plan.image;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }

        // Set form action
        document.getElementById('editForm').action = "/admin/plans/" + plan.id;

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Image Preview Input Handler
    function handleImagePreview(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
        }
    }


</script>
@endsection
