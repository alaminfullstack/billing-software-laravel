@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Revenue Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Date Range Filter -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.revenue') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                    <a href="{{ route('reports.revenue') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Revenue</h4>
                                    <h2 class="mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Invoices</h4>
                                    <h2 class="mb-0">{{ $totalInvoices }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            @if($revenueData->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Revenue Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily Revenue Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice Count</th>
                                        <th>Revenue</th>
                                        <th>Average per Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueData as $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                            <td>{{ $data->count }}</td>
                                            <td>${{ number_format($data->total, 2) }}</td>
                                            <td>${{ number_format($data->count > 0 ? $data->total / $data->count : 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th>Total</th>
                                        <th>{{ $totalInvoices }}</th>
                                        <th>${{ number_format($totalRevenue, 2) }}</th>
                                        <th>${{ number_format($totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5>No Revenue Data Found</h5>
                        <p class="text-muted">No paid invoices found for the selected date range.</p>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($revenueData->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const revenueData = @json($revenueData->map(function($item) {
        return [
            'date' => \Carbon\Carbon::parse($item->date)->format('M d'),
            'total' => $item->total,
            'count' => $item->count
        ];
    }));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.date),
            datasets: [{
                label: 'Daily Revenue',
                data: revenueData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection
