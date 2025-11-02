@extends('layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Customer Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Customers</h4>
                                    <h2 class="mb-0">{{ $customers->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Revenue</h4>
                                    <h2 class="mb-0">${{ number_format($customers->sum('total_revenue'), 2) }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Total Invoices</h4>
                                    <h2 class="mb-0">{{ $customers->sum('invoice_count') }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Avg Invoice</h4>
                                    <h2 class="mb-0">
                                        ${{ number_format($customers->sum('invoice_count') > 0 ? $customers->sum('total_revenue') / $customers->sum('invoice_count') : 0, 2) }}
                                    </h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calculator fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($customers->count() > 0)
                <!-- Top Customers Chart -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Top 10 Customers by Revenue</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="topCustomersChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Insights -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer Insights</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $topCustomer = $customers->first();
                                    $avgRevenue = $customers->avg('total_revenue');
                                    $activeCustomers = $customers->where('invoice_count', '>', 0)->count();
                                @endphp
                                <div class="mb-3">
                                    <h6 class="text-primary">Top Customer</h6>
                                    <p class="mb-0">
                                        <strong>{{ $topCustomer->customer->name }}</strong><br>
                                        <small class="text-muted">${{ number_format($topCustomer->total_revenue, 2) }}</small>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-success">Average Revenue</h6>
                                    <p class="mb-0">
                                        <strong>${{ number_format($avgRevenue, 2) }}</strong><br>
                                        <small class="text-muted">Per customer</small>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-info">Active Customers</h6>
                                    <p class="mb-0">
                                        <strong>{{ $activeCustomers }}</strong><br>
                                        <small class="text-muted">With paid invoices</small>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-warning">Inactive Customers</h6>
                                    <p class="mb-0">
                                        <strong>{{ $customers->count() - $activeCustomers }}</strong><br>
                                        <small class="text-muted">No paid invoices</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Performance Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Performance Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Invoices</th>
                                        <th>Total Revenue</th>
                                        <th>Avg Invoice</th>
                                        <th>Customer Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $index => $customerData)
                                        @php
                                            $customer = $customerData['customer'];
                                            $revenue = $customerData['total_revenue'];
                                            $invoices = $customerData['invoice_count'];
                                            $avgInvoice = $customerData['avg_invoice_value'];
                                        @endphp
                                        <tr class="{{ $index < 3 ? 'table-primary' : '' }}">
                                            <td>
                                                @if($index < 3)
                                                    <i class="fas fa-star text-warning me-1"></i>
                                                @endif
                                                <strong>{{ $customer->name }}</strong>
                                            </td>
                                            <td>{{ $customer->email }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $invoices }}</span>
                                            </td>
                                            <td>
                                                <strong class="{{ $revenue > $avgRevenue ? 'text-success' : 'text-muted' }}">
                                                    ${{ number_format($revenue, 2) }}
                                                </strong>
                                            </td>
                                            <td>${{ number_format($avgInvoice, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $customer->customer_type == 'business' ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ ucfirst($customer->customer_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $customer->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($customer->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('customers.show', $customer) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Type Analysis -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Revenue by Customer Type</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $businessRevenue = $customers->where('customer.customer_type', 'business')->sum('total_revenue');
                                    $individualRevenue = $customers->where('customer.customer_type', 'individual')->sum('total_revenue');
                                @endphp
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-primary">${{ number_format($businessRevenue, 2) }}</h4>
                                        <small class="text-muted">Business</small>
                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $customers->sum('total_revenue') > 0 ? ($businessRevenue / $customers->sum('total_revenue')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-secondary">${{ number_format($individualRevenue, 2) }}</h4>
                                        <small class="text-muted">Individual</small>
                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-secondary" 
                                                 style="width: {{ $customers->sum('total_revenue') > 0 ? ($individualRevenue / $customers->sum('total_revenue')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer Value Distribution</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $highValue = $customers->where('total_revenue', '>=', $avgRevenue)->count();
                                    $lowValue = $customers->count() - $highValue;
                                @endphp
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $highValue }}</h4>
                                        <small class="text-muted">High Value</small>
                                        <small class="d-block text-success">(>= Avg Revenue)</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning">{{ $lowValue }}</h4>
                                        <small class="text-muted">Low Value</small>
                                        <small class="d-block text-warning">(< Avg Revenue)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5>No Customers Found</h5>
                        <p class="text-muted">No customers have been registered yet.</p>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Customer
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($customers->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top Customers Chart
    const ctx = document.getElementById('topCustomersChart').getContext('2d');
    
    const topCustomers = @json($customers->take(10));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topCustomers.map(item => item.customer.name),
            datasets: [{
                label: 'Total Revenue',
                data: topCustomers.map(item => item.total_revenue),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                ],
                borderWidth: 1
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
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
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
