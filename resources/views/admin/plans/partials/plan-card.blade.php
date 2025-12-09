<div class="plan-card" style="background: white; border-radius: 25px; overflow: hidden; width: 300px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); transition: 0.3s; text-align: center; padding-bottom: 2rem; position: relative;">
    
    <!-- Plan Image with Action Buttons Overlay -->
    <div class="plan-image" style="height: 250px; width: calc(100% - 3rem); margin: 1.5rem; border-radius: 8px; object-fit: cover; background: url('{{ asset('storage/plans/' . $plan->image) }}'); background-size: cover; background-position: center; position: relative; flex-shrink: 0;">
        
        <!-- Action Buttons Overlay -->
        <div style="position: absolute; top: 15px; right: 15px; display: flex; gap: 8px; z-index: 10;">
            <button onclick="openEditModal({{ json_encode($plan) }})" style="color: var(--primary); background: rgba(255, 255, 255, 0.9); border: none; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-edit" style="font-size: 0.9rem;"></i>
            </button>
            <form id="delete-form-{{ $plan->id }}" action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="confirmDelete(event, 'delete-form-{{ $plan->id }}')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="color: #df6951; background: rgba(255, 255, 255, 0.9); border: none; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <i class="fas fa-trash" style="font-size: 0.9rem;"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="plan-content" style="padding: 0 1.5rem;">
        <div class="plan-price" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 1.1rem; color: var(--text-color);">
            <span style="font-weight: 700; color: var(--secondary);">{{ $plan->name }}</span>
            @if($plan->price == 0)
                <span class="plan-cost" style="color: var(--primary); font-weight: 700; font-size: 1.3rem;">-</span>
            @elseif($plan->hasActiveDiscount())
                <div class="price-container" style="text-align: right;">
                    <span class="original-price" style="text-decoration: line-through; color: #888; font-size: 0.8rem; display: block;">Rp {{ $plan->price >= 1000 ? round($plan->price / 1000) . 'K' : number_format($plan->price, 0, ',', '.') }}</span>
                    <span class="plan-cost" style="color: #e63946; font-weight: 700; font-size: 1.3rem;">Rp {{ $plan->discount_price >= 1000 ? round($plan->discount_price / 1000) . 'K' : number_format($plan->discount_price, 0, ',', '.') }}</span>
                </div>
            @else
                <span class="plan-cost" style="color: var(--primary); font-weight: 700; font-size: 1.3rem;">Rp {{ $plan->price >= 1000 ? round($plan->price / 1000) . 'K' : number_format($plan->price, 0, ',', '.') }}</span>
            @endif
        </div>

        <div style="text-align: left; margin-bottom: 1rem; color: var(--text-color); font-size: 0.9rem;">
            <p style="margin-bottom: 15px; font-style: italic; color: #666;">{{ $plan->description }}</p>
            @if($plan->features)
                @foreach($plan->features as $feature)
                    <div style="margin-bottom: 8px;">
                        <i class="fas fa-check" style="margin-right: 10px; color: var(--primary);"></i> {{ $feature }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
