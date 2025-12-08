@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2>My Subscriptions</h2>
        <p>Manage your billing and plan details.</p>
    </div>

    @if(!$subscription)
        <div class="white-card" style="padding: 3rem; text-align: center; max-width: 600px; margin: 0 auto;">
            <div style="margin-bottom: 1.5rem;">
                <i class="fas fa-ghost" style="font-size: 3rem; color: #e0e0e0;"></i>
            </div>
            <h3 style="margin-bottom: 1rem;">No Active Subscription</h3>
            <p style="color: #666; margin-bottom: 2rem;">You don't have any active hosting plan yet.</p>
            <button onclick="openModal('upgradeModal')" class="btn btn-primary" style="padding: 12px 25px; border-radius: 50px;">
                <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i> Browse Plans
            </button>
        </div>
    @else
        <div class="charts-grid">
            
            <!-- Current Plan Card (Main Content) -->
            <div class="white-card" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: var(--primary);"></div>
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                    <div>
                        <h4 style="color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">
                            Current Plan
                        </h4>
                        <h3 style="font-size: 1.8rem; text-transform: capitalize; margin-bottom: 0.5rem;">
                            {{ str_replace('_', ' ', $subscription->plan_type) }} Hosting
                        </h3>
                         <div>
                            @if($subscription->status == 'active')
                                <span class="badge" style="background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid #c8e6c9;">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @elseif($subscription->status == 'pending')
                                <span class="badge" style="background: #fff3e0; color: #ef6c00; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid #ffe0b2;">
                                    <i class="fas fa-clock"></i> Pending Payment
                                </span>
                            @else
                                <span class="badge" style="background: #ffebee; color: #c62828; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid #ffcdd2;">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--primary); font-size: 1.8rem; font-weight: 700;">
                            Rp {{ number_format($subscription->price, 0, ',', '.') }}
                        </div>
                        <div style="color: #999; font-size: 0.9rem;">
                            {{ $subscription->plan_type == '1_month' || $subscription->plan_type == 'starter' ? 'billed monthly' : 'billed every 2 months' }}
                        </div>
                    </div>
                </div>

                <div style="background: var(--background); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid transparent;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                        <span style="color: #666;"><i class="fas fa-calendar-alt" style="margin-right: 8px; color: var(--secondary);"></i> Start Date</span>
                        <span style="font-weight: 600;">
                            {{ $subscription->starts_at ? $subscription->starts_at->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;"><i class="fas fa-hourglass-end" style="margin-right: 8px; color: var(--secondary);"></i> Ends On</span>
                        <span style="font-weight: 600;">
                            {{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>

                @if($subscription->status == 'active')
                    <button type="button" onclick="openModal('upgradeModal')" class="btn btn-primary" style="width: 100%; display: block; text-align: center;">
                        Extend / Upgrade Plan
                    </button>
                @elseif($subscription->status == 'pending')
                    <div class="alert alert-warning" style="background: #fff3e0; color: #ef6c00; padding: 1rem; border-radius: 10px; font-size: 0.9rem; border: 1px solid #ffe0b2;">
                        <i class="fas fa-info-circle"></i> Please complete your payment and send proof via email or WhatsApp to activate.
                    </div>
                @endif
            </div>

            <!-- Features Summary or Usage (Sidebar Column) -->
            <div class="white-card">
                <h3 style="margin-bottom: 1.5rem;">Plan Features</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: rgba(223, 105, 81, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-color);">{{ number_format($user->storage_limit / 1073741824, 0) }} GB</div>
                            <div style="color: #666; font-size: 0.9rem;">Storage Limit</div>
                        </div>
                    </li>
                    <li style="margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: rgba(223, 105, 81, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                             <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-color);">Unlimited</div>
                            <div style="color: #666; font-size: 0.9rem;">Databases</div>
                        </div>
                    </li>
                     <li style="margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: rgba(223, 105, 81, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                         <div>
                             <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-color);">Unlimited</div>
                            <div style="color: #666; font-size: 0.9rem;">Email Accounts</div>
                        </div>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: flex-start; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: rgba(223, 105, 81, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
                            <i class="fab fa-github"></i>
                        </div>
                         <div>
                             <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-color);">Ready</div>
                            <div style="color: #666; font-size: 0.9rem;">GitHub Auto-Deploy</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    @endif
</div>

<!-- Upgrade Modal -->
<div id="upgradeModal" class="custom-modal">
    <div class="modal-content" style="max-width: 600px;">
        <h3 style="margin-bottom: 1rem;">Select Plan to Upgrade/Extend</h3>
        <p style="color: #666; margin-bottom: 2rem;">Choose a plan below. You will be redirected to WhatsApp to confirm your request with our admin.</p>
        
        <div class="plan-options-grid">
            @foreach($plans as $plan)
            <label class="plan-option">
                <input type="radio" name="selected_plan" value="{{ $plan->name }} (Rp {{ number_format($plan->price, 0, ',', '.') }})" {{ $loop->first ? 'checked' : '' }}>
                <div class="plan-option-card">
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-price">Rp {{ number_format($plan->price, 0, ',', '.') }}</div>
                    <div class="plan-desc">{{ Str::limit($plan->description, 50) }}</div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="modal-actions" style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn btn-secondary" onclick="closeModal('upgradeModal')">Cancel</button>
            <button class="btn btn-primary" onclick="proceedToWhatsapp()">
                <i class="fab fa-whatsapp"></i> Confirm & Chat Admin
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modal Styles */
    .custom-modal {
        display: none;
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .custom-modal.show {
        display: flex;
        animation: fadeIn 0.3s forwards;
    }
    .modal-content {
        background: var(--white); padding: 2.5rem; border-radius: 20px; width: 90%;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
    }
    
    /* Plan Selection Grid */
    .plan-options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    .plan-option input { display: none; }
    .plan-option-card {
        border: 2px solid #eee;
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }
    .plan-option input:checked + .plan-option-card {
        border-color: var(--primary);
        background: rgba(223, 105, 81, 0.05);
    }
    .plan-name { font-weight: 700; color: var(--secondary); margin-bottom: 5px; }
    .plan-price { color: var(--primary); font-weight: 600; margin-bottom: 5px; }
    .plan-desc { font-size: 0.8rem; color: #888; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endpush

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }
    function proceedToWhatsapp() {
        // Get selected plan
        const selected = document.querySelector('input[name="selected_plan"]:checked').value;
        const userName = "{{ Auth::user()->name }}";
        const userEmail = "{{ Auth::user()->email }}";
        
        const message = `Halo Admin Hostoo, saya ingin melakukan Extend/Upgrade subscription akun saya.\n\nNama: ${userName}\nEmail: ${userEmail}\nPlan Pilihan: ${selected}\n\nMohon bantuannya. Terima kasih.`;
        
        // Placeholder phone number - replace with actual admin number
        const phoneNumber = "62895627447432"; 
        
        const waUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
        closeModal('upgradeModal');
    }
    
    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('upgradeModal');
        if (event.target == modal) closeModal('upgradeModal');
    }
</script>
@endpush
@endsection
