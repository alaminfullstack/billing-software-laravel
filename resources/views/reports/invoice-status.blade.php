@extends('layouts.app')

@section('title', 'Invoice Status Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Invoice Status Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            @if($statusData->count() > 0)
                <!-- Summary Cards -->
                <div class="row mb-4">
                    @foreach($statusData as $status)
                        @php
                            $statusColors = [
                                'draft' => 'bg-secondary',
                                'sent' => 'bg-info',
                                'paid' => 'bg-success',
                                'overdue' => 'bg-danger',
                                'cancelled' => 'bg-dark'
                            ];
                            $statusIcons = [
                                'draft' => 'fas fa-edit',
                                'sent' => 'fas fa-paper-plane',
                                'paid' => 'fas fa-check-circle',
                                'overdue' => 'fas fa-exclamation-triangle',
                                'cancelled' => 'fas fa-times-circle'
                            ];
                        @endphp
                        <div class="col-md-2">
                            <div class="card {{ $statusColors[$status->status] ?? 'bg-primary' }} text-white">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="{{ $statusIcons[$status->status] ?? 'fas fa-file-invoice' }} fa-2x"></i>
                                    </div>
                                    <h4 class="card-title">{{ ucfirst($status->status) }}</h4>
                                    <h2 class="mb-1">{{ $status->count }}</h2>
                                    <small>${{ number_format($status->total_amount, 2) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Status Overview -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Invoice Distribution by Status</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statusChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Key Metrics</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $totalInvoices = $statusData->sum('count');
                                    $totalAmount = $statusData->sum('total_amount');
                                    $totalPaid = $statusData->where('status', 'paid')->sum('total_amount');
                                    $totalOutstanding = $statusData->sum('balance_amount');
                                    $paymentRate = $totalAmount > 0 ? ($totalPaid / $totalAmount) * 100 : 0;
                                @endphp
                                
                                <div class="mb-3">
                                    <h6 class="text-primary">Total Invoices</h6>
                                    <h3 class="mb-0">{{ $totalInvoices }}</h3>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-success">Total Value</h6>
                                    <h3 class="mb-0">${{ number_format($totalAmount, 2) }}</h3>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-info">Paid Amount</h6>
                                    <h3 class="mb-0 text-success">${{ number_format($totalPaid, 2) }}</h3>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-warning">Outstanding</h6>
                                    <h3 class="mb-0 text-warning">${{ number_format($totalOutstanding, 2) }}</h3>
                                </div>
                                
                                <hr>
                                
                                <div class="text-center">
                                    <h6 class="text-success">Collection Rate</h6>
                                    <div class="display-4 text-success">{{ number_format($paymentRate, 1) }}%</div>
                                
                                    <!-- Progress Bar -->
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ $paymentRate }}%">
                                        </div>
                                        <div class="progress-bar bg-warning" 
                                             style="width: {{ 100 - $paymentRate }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-success">Paid</small>
                                        <small class="text-warning">Outstanding</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Status Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Invoice Status Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Outstanding</th>
                                        <th>Avg Invoice Value</th>
                                        <th>Percentage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusData as $status)
                                        @php
                                            $percentage = $totalInvoices > 0 ? ($status->count / $totalInvoices) * 100 : 0;
                                            $avgValue = $status->count > 0 ? $status->total_amount / $status->count : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge {{ $statusColors[$status->status] ?? 'bg-primary' }}">
                                                    <i class="{{ $statusIcons[$status->status] ?? 'fas fa-file-invoice' }} me-1"></i>
                                                    {{ ucfirst($status->status) }}
                                                </span>
                                            </td>
                                            <td><strong>{{ $status->count }}</strong></td>
                                            <td>${{ number_format($status->total_amount, 2) }}</td>
                                            <td>${{ number_format($status->paid_amount, 2) }}</td>
                                            <td>${{ number_format($status->balance_amount, 2) }}</td>
                                            <td>${{ number_format($avgValue, 2) }}</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar {{ $statusColors[$status->status] ?? 'bg-primary' }}" 
                                                         style="width: {{ $percentage }}%">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($status->status == 'sent')
                                                    <a href="{{ route('invoices.index') }}?status=sent" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @elseif($status->status == 'paid')
                                                    <a href="{{ route('invoices.index') }}?status=paid" 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @elseif($status->status == 'overdue')
                                                    <a href="{{ route('invoices.index') }}?status=overdue" 
                                                       class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ route('invoices.index') }}?status={{ $status->status }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th>Total</th>
                                        <th>{{ $totalInvoices }}</th>
                                        <th>${{ number_format($totalAmount, 2) }}</th>
                                        <th>${{ number_format($totalPaid, 2) }}</th>
                                        <th>${{ number_format($totalOutstanding, 2) }}</th>
                                        <th>${{ number_format($totalInvoices > 0 ? $totalAmount / $totalInvoices : 0, 2) }}</th>
                                        <th>100%</th>
                                        <th>-</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Items -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> Action Required
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $overdueCount = $statusData->where('status', 'overdue')->sum('count');
                                    $sentCount = $statusData->where('status', 'sent')->sum('count');
                                @endphp
                                
                                @if($overdueCount > 0)
                                    <div class="alert alert-danger">
                                        <strong>{{ $overdueCount }}</strong> invoice(s) are overdue and require immediate attention.
                                    </div>
                                @endif
                                
                                @if($sentCount > 0)
                                    <div class="alert alert-info">
                                        <strong>{{ $sentCount }}</strong> invoice(s) have been sent but not yet paid.
                                    </div>
                                @endif
                                
                                @if($overdueCount == 0 && $sentCount == 0)
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> All invoices are up to date!
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-tasks"></i> Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create New Invoice
                                    </a>
                                    @if($sentCount > 0)
                                        <a href="{{ route('invoices.index') }}?status=sent" class="btn btn-info">
                                            <i class="fas fa-list"></i> View Sent Invoices
                                        </a>
                                    @endif
                                    @if($overdueCount > 0)
                                        <a href="{{ route('invoices.index') }}?status=overdue" class="btn btn-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Follow Up Overdue
                                        </a>
                                    @endif
                                    <a href="{{ route('reports.revenue') }}" class="btn btn-success">
                                        <i class="fas fa-chart-line"></i> Revenue Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                        <h5>No Invoice Data Found</h5>
                        <p class="text-muted">No invoices have been created yet.</p>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($statusData->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    
    const statusData = @json($statusData);
    const colors = ['#6C757D', '#17A2B8', '#28A745', '#DC3545', '#343A40'];
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: colors.slice(0, statusData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = statusData.reduce((sum, item) => sum + item.count, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
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
