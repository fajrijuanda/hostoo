@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2 style="font-weight: 700; color: #333;">Admin Dashboard</h2>
        <p style="color: #666;">Real-time overview of system performance and growth.</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        
        <!-- Total Users (Excluding Admin) -->
        <div class="card white-card" style="padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: relative; overflow: hidden; height: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h4 style="margin: 0; font-size: 2rem; font-weight: 700;">{{ $totalUsers }}</h4>
                    <div class="stat-label">Total Clients</div>
                </div>
                <!-- Icon -->
            </div>
            <div class="stat-meta">
                @if($userGrowth >= 0)
                    <span class="badge" style="color: #2e7d32; font-weight: 600; background: #e8f5e9; padding: 2px 6px; border-radius: 4px;">
                        <i class="fas fa-arrow-up"></i> {{ number_format($userGrowth, 1) }}%
                    </span>
                @else
                    <span class="badge" style="color: #c62828; font-weight: 600; background: #ffebee; padding: 2px 6px; border-radius: 4px;">
                        <i class="fas fa-arrow-down"></i> {{ number_format(abs($userGrowth), 1) }}%
                    </span>
                @endif
                <span>vs previous day</span>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h4 style="margin: 0; font-size: 2rem; font-weight: 700;">{{ $activeSubscriptions }}</h4>
                    <div class="stat-label">Active Subscriptions</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e8f5e9; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-certificate" style="color: #2e7d32; font-size: 1.2rem;"></i>
                </div>
            </div>
             <div class="stat-meta">
                @if($subGrowth >= 0)
                    <span class="badge" style="color: #2e7d32; font-weight: 600; background: #e8f5e9; padding: 2px 6px; border-radius: 4px;">
                        <i class="fas fa-arrow-up"></i> {{ number_format($subGrowth, 1) }}%
                    </span>
                @else
                    <span class="badge" style="color: #c62828; font-weight: 600; background: #ffebee; padding: 2px 6px; border-radius: 4px;">
                        <i class="fas fa-arrow-down"></i> {{ number_format(abs($subGrowth), 1) }}%
                    </span>
                @endif
                <span>vs previous day</span>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h4 style="margin: 0; font-size: 2rem; font-weight: 700;">{{ $pendingRequests }}</h4>
                    <div class="stat-label">Pending Requests</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff3e0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clock" style="color: #ef6c00; font-size: 1.2rem;"></i>
                </div>
            </div>
            <div class="stat-meta">
                Needs Action
            </div>
        </div>

        <!-- Revenue Estimate -->
        <div class="card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                     @php
                        $revenueFormatted = number_format($revenue / 1000, 0, ',', '.') . 'k';
                    @endphp
                    <h4 style="margin: 0; font-size: 2rem; font-weight: 700;">{{ $revenueFormatted }}</h4>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f3e5f5; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-wallet" style="color: #7b1fa2; font-size: 1.2rem;"></i>
                </div>
            </div>
             <div class="stat-meta">
                All time
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        
        <!-- Revenue Chart -->
        <div class="card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h4 style="margin-bottom: 1.5rem; color: #333; font-weight: 600;">Revenue Trends</h4>
            <div style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Plan Distribution -->
        <div class="card" style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h4 style="margin-bottom: 1.5rem; color: #333; font-weight: 600;">Plan Distribution</h4>
            <div style="height: 300px; position: relative;">
                <canvas id="planChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme Helper
        const isDarkMode = () => document.body.classList.contains('dark-mode');
        const getTextColor = () => isDarkMode() ? '#e0e0e0' : '#666';
        const getGridColor = () => isDarkMode() ? 'rgba(255, 255, 255, 0.1)' : '#f0f0f0';
        
        // Revenue Chart
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: @json($revenueLabels),
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: @json($revenueData),
                    borderColor: '#FF6B6B',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#FF6B6B',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: getGridColor() },
                        ticks: { color: getTextColor() }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: getTextColor() }
                    }
                }
            }
        });

        // Plan Chart
        const ctxPlan = document.getElementById('planChart').getContext('2d');
        const planChart = new Chart(ctxPlan, {
            type: 'doughnut',
            data: {
                labels: ['Starter', 'Pro'],
                datasets: [{
                    data: @json($planStats),
                    backgroundColor: [
                        '#4facfe',
                        '#8F55D5'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { color: getTextColor() }
                    }
                },
                layout: { padding: 20 },
                elements: {
                    arc: {
                        borderWidth: 0
                    }
                }
            }
        });

        // Dynamic Theme Update
        function updateCharts() {
            const textColor = getTextColor();
            const gridColor = getGridColor();

            // Update Revenue Chart
            revenueChart.options.scales.y.grid.color = gridColor;
            revenueChart.options.scales.y.ticks.color = textColor;
            revenueChart.options.scales.x.ticks.color = textColor;
            revenueChart.update();

            // Update Plan Chart
            planChart.options.plugins.legend.labels.color = textColor;
            planChart.update();
        }

        // Watch for Class Changes on Body
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    updateCharts();
                }
            });
        });

        observer.observe(document.body, { attributes: true });
    });
</script>
<style>
    /* Stats Card Typography */
    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }
    .stat-meta {
        font-size: 0.85rem;
        color: #888;
    }

    /* Dark Mode Overrides */
    body.dark-mode .stat-label {
        color: #e0e0e0 !important;
    }
    body.dark-mode .stat-meta {
        color: #b0b0b0 !important;
    }
    
    /* Ensure card backgrounds are correct in DM (Redundant if theme.css handles it, but safety first) */
    body.dark-mode .stats-grid .card {
        background-color: #1E1E1E !important;
        border: 1px solid #333 !important;
    }
    body.dark-mode .stats-grid .card h4 {
        color: white !important;
    }
</style>

@endsection
