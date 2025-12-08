@extends('layouts.app')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 2rem; font-weight: 600; color: #333;">Subscription Management</h2>

    <!-- Pending Subscriptions -->
    <div style="margin-bottom: 3rem;">
        <h3 style="margin-bottom: 1rem; color: #ef6c00; font-size: 1.2rem; border-left: 4px solid #ef6c00; padding-left: 10px;">Pending Requests</h3>
        
        <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #fff3e0;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; color: #d84315;">User</th>
                        <th style="padding: 1rem; text-align: left; color: #d84315;">Plan</th>
                        <th style="padding: 1rem; text-align: left; color: #d84315;">Price</th>
                        <th style="padding: 1rem; text-align: left; color: #d84315;">Date</th>
                        <th style="padding: 1rem; text-align: right; color: #d84315;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingSubscriptions as $sub)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem;">
                            <div style="font-weight: 500;">{{ $sub->user->name }}</div>
                            <div style="font-size: 0.85rem; color: #888;">{{ $sub->user->email }}</div>
                        </td>
                        <td style="padding: 1rem;">
                            @if(in_array($sub->plan_type, ['1_month', 'starter']))
                                <span class="badge" style="background: #e3f2fd; color: #0d47a1; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem;">Starter (1 Mo)</span>
                            @else
                                <span class="badge" style="background: #f3e5f5; color: #4a148c; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem;">Pro (2 Mo)</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">Rp {{ number_format($sub->price, 0, ',', '.') }}</td>
                        <td style="padding: 1rem; color: #666;">
                            {{ $sub->created_at->format('d M Y H:i') }}
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <a href="https://docs.google.com/forms/d/1gLxJ6NlZSzfYUSLeo5rmK_GweU1ACeZAo70urQqvdAY/edit#responses" target="_blank" class="btn-icon" style="background: #17a2b8; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; margin-right: 5px; font-weight: 600; text-decoration: none; display: inline-block; font-size: 0.85rem;" title="Check Payment Proof">
                                <i class="fas fa-file-invoice"></i> Check Proof
                            </a>
                            <form id="approve-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.approve', $sub->id) }}" method="POST" style="display: inline-block;" onsubmit="confirmApprove(event, 'approve-form-{{ $sub->id }}')">
                                @csrf
                                <button class="btn-icon" style="background: #28a745; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; margin-right: 5px; font-weight: 600;" title="Approve">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form id="reject-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.reject', $sub->id) }}" method="POST" style="display: inline-block;" onsubmit="confirmReject(event, 'reject-form-{{ $sub->id }}')">
                                @csrf
                                <button class="btn-icon" style="background: #dc3545; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; font-weight: 600;" title="Reject">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #999; font-style: italic;">No pending requests.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Subscriptions -->
    <div>
        <h3 style="margin-bottom: 1rem; color: #2e7d32; font-size: 1.2rem; border-left: 4px solid #2e7d32; padding-left: 10px;">Active Subscriptions</h3>
        
        <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #e8f5e9;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; color: #1b5e20;">User</th>
                        <th style="padding: 1rem; text-align: left; color: #1b5e20;">Plan</th>
                        <th style="padding: 1rem; text-align: left; color: #1b5e20;">Storage</th>
                        <th style="padding: 1rem; text-align: left; color: #1b5e20;">Expires</th>
                        <th style="padding: 1rem; text-align: left; color: #1b5e20;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeSubscriptions as $sub)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem;">
                            <div style="font-weight: 500;">{{ $sub->user->name }}</div>
                            <div style="font-size: 0.85rem; color: #888;">{{ $sub->user->email }}</div>
                        </td>
                        <td style="padding: 1rem;">
                            @if(in_array($sub->plan_type, ['1_month', 'starter']))
                                <span style="font-weight:600; color: #0d47a1;">Starter</span>
                            @else
                                <span style="font-weight:600; color: #4a148c;">Pro</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                             @if($sub->user->storage_limit > 0)
                                @php
                                    $usage = $sub->user->storage_usage;
                                    $limit = $sub->user->storage_limit;
                                    $percent = min(100, ($usage / $limit) * 100);
                                    $usageGB = number_format($usage / 1073741824, 2);
                                    $limitGB = number_format($limit / 1073741824, 0); 
                                    $color = $percent > 90 ? '#dc3545' : '#28a745';
                                @endphp
                                <div style="font-size: 0.85rem; font-weight: 600;">{{ $usageGB }} / {{ $limitGB }} GB</div>
                                <div style="width: 100px; height: 5px; background: #eee; border-radius: 2px; margin-top: 3px;">
                                    <div style="width: {{ $percent }}%; height: 100%; background: {{ $color }}; border-radius: 2px;"></div>
                                </div>
                             @else
                                <span style="color: #ccc;">-</span>
                             @endif
                        </td>
                        <td style="padding: 1rem;">
                            {{ $sub->ends_at ? $sub->ends_at->format('d M Y') : 'N/A' }}
                            <br>
                            <small class="{{ $sub->ends_at && $sub->ends_at->isPast() ? 'text-danger' : 'text-success' }}">
                                @if($sub->ends_at)
                                    @php
                                        $daysLeft = now()->diffInDays($sub->ends_at, false);
                                        $hoursLeft = now()->diffInHours($sub->ends_at, false);
                                    @endphp
                                    @if($daysLeft > 0)
                                        {{ ceil($daysLeft) }} days remaining
                                    @elseif($hoursLeft > 0)
                                        {{ ceil($hoursLeft) }} hours remaining
                                    @else
                                        Expired
                                    @endif
                                @endif
                            </small>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge" style="background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem;">Active</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #999;">No active subscriptions.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
</script>
@endsection
