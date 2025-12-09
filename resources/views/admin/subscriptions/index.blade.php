@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem;">Subscription Management</h2>
            <p style="color: var(--text-color); opacity: 0.8; margin: 0;">Monitor and manage client subscriptions and requests.</p>
        </div>
        <!-- Optional: Add filters or buttons here if needed later -->
    </div>

    <!-- Layout Grid -->
    <div style="display: grid; gap: 2rem; grid-template-columns: 1fr;">
        
        <!-- Pending Requests Section -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; border: none;">
            <div style="padding: 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff3e0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clock" style="color: #ef6c00; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; font-weight: 700; color: #ef6c00;">Pending Requests</h5>
                    <span style="font-size: 0.85rem; color: #999;">Awaiting your approval</span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fcfcfc; border-bottom: 1px solid #eee;">
                        <tr>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">User</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Plan</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Price</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Request Date</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSubscriptions as $sub)
                        <tr style="border-bottom: 1px solid #f9f9f9; transition: background 0.2s;">
                            <td style="padding: 1.2rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 35px; height: 35px; background: #f1f3f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #adb5bd; font-size: 0.9rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--secondary);">{{ $sub->user->name }}</div>
                                        <div style="font-size: 0.8rem; color: #888;">{{ $sub->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.2rem 1.5rem;">
                                @if(in_array($sub->plan_type, ['1_month', 'starter']))
                                    <span style="background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-rocket"></i> Starter
                                    </span>
                                @else
                                    <span style="background: #f3e5f5; color: #7b1fa2; padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-crown"></i> Pro
                                    </span>
                                @endif
                                <div style="font-size: 0.75rem; color: #999; margin-top: 4px; padding-left: 5px;">
                                    {{ in_array($sub->plan_type, ['1_month', 'starter']) ? '1 Month' : '2 Months' }} Duration
                                </div>
                            </td>
                            <td style="padding: 1.2rem 1.5rem; font-weight: 600; color: var(--text-color);">
                                Rp {{ number_format($sub->price, 0, ',', '.') }}
                            </td>
                            <td style="padding: 1.2rem 1.5rem; color: #666; font-size: 0.9rem;">
                                {{ $sub->created_at->format('d M Y') }}
                                <small style="display: block; color: #999;">{{ $sub->created_at->format('H:i') }}</small>
                            </td>
                            <td style="padding: 1.2rem 1.5rem; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="https://docs.google.com/forms/d/1gLxJ6NlZSzfYUSLeo5rmK_GweU1ACeZAo70urQqvdAY/edit#responses" target="_blank" style="background: #e0f7fa; color: #0097a7; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;" title="Check Payment Proof">
                                        <i class="fas fa-file-invoice" style="font-size: 0.9rem;"></i>
                                    </a>
                                    
                                    <form id="approve-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.approve', $sub->id) }}" method="POST" onsubmit="confirmApprove(event, 'approve-form-{{ $sub->id }}')">
                                        @csrf
                                        <button type="submit" style="background: #e8f5e9; color: #2e7d32; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;" title="Approve">
                                            <i class="fas fa-check" style="font-size: 0.9rem;"></i>
                                        </button>
                                    </form>

                                    <form id="reject-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.reject', $sub->id) }}" method="POST" onsubmit="confirmReject(event, 'reject-form-{{ $sub->id }}')">
                                        @csrf
                                        <button type="submit" style="background: #ffebee; color: #c62828; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;" title="Reject">
                                            <i class="fas fa-times" style="font-size: 0.9rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem 2rem; color: #adb5bd;">
                                <div style="margin-bottom: 1rem; font-size: 2rem; opacity: 0.3;"><i class="fas fa-inbox"></i></div>
                                <p style="margin: 0; font-style: italic;">No pending requests to handle.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Subscriptions Section -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; border: none;">
             <div style="padding: 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e8f5e9; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-server" style="color: #2e7d32; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; font-weight: 700; color: #2e7d32;">Active Subscriptions</h5>
                    <span style="font-size: 0.85rem; color: #999;">Currently running services</span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fcfcfc; border-bottom: 1px solid #eee;">
                         <tr>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">User</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Active Plan</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Storage Usage</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Expiration</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeSubscriptions as $sub)
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 1.2rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 35px; height: 35px; background: #f1f3f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #adb5bd; font-size: 0.9rem;">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--secondary);">{{ $sub->user->name }}</div>
                                        <div style="font-size: 0.8rem; color: #888;">{{ $sub->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.2rem 1.5rem;">
                                <span style="font-weight: 700; color: var(--secondary); font-size: 0.95rem;">
                                    {{ in_array($sub->plan_type, ['1_month', 'starter']) ? 'Starter Plan' : 'Pro Plan' }}
                                </span>
                            </td>
                            <td style="padding: 1.2rem 1.5rem; width: 25%;">
                                @if($sub->user->storage_limit > 0)
                                    @php
                                        $usage = $sub->user->storage_usage;
                                        $limit = $sub->user->storage_limit;
                                        $percent = min(100, ($usage / $limit) * 100);
                                        $usageText = \App\Helpers\FormatHelper::formatBytes($usage) ?? number_format($usage / 1073741824, 2) . ' GB';
                                        $limitText = \App\Helpers\FormatHelper::formatBytes($limit) ?? number_format($limit / 1073741824, 0) . ' GB';
                                        
                                        // Quick fallback formatting if helper missing or simpler needed
                                        $usageGB = number_format($usage / 1073741824, 2);
                                        $limitGB = number_format($limit / 1073741824, 0);

                                        $barColor = $percent > 90 ? '#dc3545' : '#28a745';
                                        if($percent > 75 && $percent <= 90) $barColor = '#ffc107';
                                    @endphp
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 5px; color: #666;">
                                        <span>{{ $usageGB }} GB used</span>
                                        <span>{{ $limitGB }} GB</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: #eaeff2; border-radius: 3px; overflow: hidden;">
                                        <div style="width: {{ $percent }}%; height: 100%; background: {{ $barColor }}; border-radius: 3px; transition: width 0.5s ease;"></div>
                                    </div>
                                @else
                                    <span style="color: #ccc; font-style: italic;">No Usage Data</span>
                                @endif
                            </td>
                            <td style="padding: 1.2rem 1.5rem;">
                                <div style="font-weight: 500; color: var(--text-color);">
                                    {{ $sub->ends_at ? $sub->ends_at->format('d M Y') : 'N/A' }}
                                </div>
                                @if($sub->ends_at)
                                    @php
                                        $daysLeft = now()->diffInDays($sub->ends_at, false);
                                    @endphp
                                    @if($daysLeft > 0)
                                        <span style="font-size: 0.75rem; color: #28a745; background: #e8f5e9; padding: 2px 6px; border-radius: 4px;">{{ ceil($daysLeft) }} days left</span>
                                    @else
                                        <span style="font-size: 0.75rem; color: #dc3545; background: #ffebee; padding: 2px 6px; border-radius: 4px;">Expired</span>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 1.2rem 1.5rem; text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <span style="background: #e0f2f1; color: #00695c; padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                    
                                    <form id="delete-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.delete', $sub->id) }}" method="POST" onsubmit="confirmDelete(event, 'delete-form-{{ $sub->id }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #ffebee; color: #c62828; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;" title="Delete / Reset">
                                            <i class="fas fa-trash" style="font-size: 0.9rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem 2rem; color: #adb5bd;">
                                <p style="margin: 0; font-style: italic;">No active subscriptions found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmApprove(e, formId) {
        e.preventDefault();
        Hostoo.alert({
            title: 'Approve Subscription?',
            text: 'This will activate the plan for the user immediately.',
            type: 'success',
            showCancel: true,
            confirmText: 'Yes, Approve'
        }).then(confirmed => {
            if(confirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmReject(e, formId) {
        e.preventDefault();
        Hostoo.alert({
            title: 'Reject Subscription?',
            text: 'Are you sure you want to reject this request?',
            type: 'warning',
            showCancel: true,
            confirmText: 'Yes, Reject'
        }).then(confirmed => {
            if(confirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmDelete(e, formId) {
        e.preventDefault();
        Hostoo.alert({
            title: 'Delete Subscription?',
            text: 'This will remove the subscription record. The user will be able to request a new plan.',
            type: 'warning',
            showCancel: true,
            confirmText: 'Yes, Delete'
        }).then(confirmed => {
            if(confirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection
