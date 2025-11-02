@extends('layouts.app')

@section('title', 'Financial Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Financial Dashboard</h1>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 200px;">
                        <input type="month" class="form-control" id="report-month" value="{{ date('Y-m') }}">
                        <button class="btn btn-outline-secondary" type="button" onclick="loadReport()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Key Financial Metrics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Revenue (MTD)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($revenueMTD ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Expenses (MTD)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($expensesMTD ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Net Profit (MTD)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format(($revenueMTD ?? 0) - ($expensesMTD ?? 0), 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Outstanding Invoices
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $outstandingInvoices ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Dropdown Header:</div>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Expense Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="expenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Row -->
            <div class="row">
                <div class="col-12">
                    @include('reports.partials.cash_flow')
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-6">
                    @include('reports.partials.balance_sheet')
                </div>
                <div class="col-lg-6">
                    @include('reports.partials.customer_summary')
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    @include('reports.partials.product_performance')
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-block w-100">
                                        <i class="fas fa-plus"></i> Create Invoice
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('expenses.create') }}" class="btn btn-success btn-block w-100">
                                        <i class="fas fa-plus"></i> Add Expense
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('payments.create') }}" class="btn btn-info btn-block w-100">
                                        <i class="fas fa-plus"></i> Record Payment
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('reports.index') }}" class="btn btn-warning btn-block w-100">
                                        <i class="fas fa-chart-bar"></i> View Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Initialize charts when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
    });

    function initializeCharts() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($monthlyRevenue ?? array_fill(0, 12, 0)) !!},
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Expenses',
                        data: {!! json_encode($monthlyExpenses ?? array_fill(0, 12, 0)) !!},
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.4
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
                    }
                }
            });
        }

        // Expense Chart
        const expenseCtx = document.getElementById('expenseChart');
        if (expenseCtx) {
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($expenseCategories ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($expenseByCategory ?? []) !!},
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    function loadReport() {
        const month = document.getElementById('report-month').value;
        // Reload the page with new month parameter
        window.location.href = window.location.pathname + '?month=' + month;
    }

    // Auto-refresh data every 5 minutes
    setInterval(() => {
        loadReport();
    }, 300000);
</script>

<style>
    .chart-area {
        height: 320px;
    }
    
    .chart-pie {
        height: 320px;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
</style>
@endsection
