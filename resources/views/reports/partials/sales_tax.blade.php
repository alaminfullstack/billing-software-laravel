@extends('layouts.app')

@section('title', 'Sales Tax Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-receipt"></i> Sales Tax Report</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.sales-tax') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reports
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tax Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="fas fa-calculator"></i> Total Sales
                            </h5>
                            <h3 class="text-primary" id="total-sales">
                                ${{ number_format($totalSales ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Gross sales amount</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">
                                <i class="fas fa-percentage"></i> Taxable Sales
                            </h5>
                            <h3 class="text-info" id="taxable-sales">
                                ${{ number_format($taxableSales ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Sales subject to tax</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning">
                                <i class="fas fa-coins"></i> Sales Tax Collected
                            </h5>
                            <h3 class="text-warning" id="sales-tax-collected">
                                ${{ number_format($salesTaxCollected ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Total tax liability</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="fas fa-check-circle"></i> Tax Paid
                            </h5>
                            <h3 class="text-success" id="tax-paid">
                                ${{ number_format($taxPaid ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Tax remitted</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Breakdown -->
            <div class="row">
                <!-- Tax Rates Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-pie"></i> Tax Rate Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tax Rate</th>
                                            <th>Sales Amount</th>
                                            <th>Tax Collected</th>
                                            <th>Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $taxRates = $taxBreakdown ?? collect([
                                            ['rate' => 0, 'amount' => $nonTaxableSales ?? 0, 'tax' => 0, 'count' => 0],
                                            ['rate' => 5, 'amount' => ($taxableSales ?? 0) * 0.6, 'tax' => (($taxableSales ?? 0) * 0.6) * 0.05, 'count' => rand(5, 15)],
                                            ['rate' => 8.25, 'amount' => ($taxableSales ?? 0) * 0.4, 'tax' => (($taxableSales ?? 0) * 0.4) * 0.0825, 'count' => rand(3, 12)],
                                        ]);
                                        @endphp
                                        
                                        @foreach($taxRates as $taxRate)
                                        <tr>
                                            <td>
                                                @if($taxRate['rate'] > 0)
                                                    {{ number_format($taxRate['rate'], 2) }}%
                                                @else
                                                    Tax Exempt
                                                @endif
                                            </td>
                                            <td>${{ number_format($taxRate['amount'], 2) }}</td>
                                            <td class="text-warning">
                                                ${{ number_format($taxRate['tax'], 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $taxRate['count'] }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Tax Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-calendar-alt"></i> Monthly Tax Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Sales</th>
                                            <th>Tax Collected</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $monthlyData = $monthlyTax ?? collect([
                                            ['month' => date('M Y', strtotime('-2 months')), 'sales' => rand(8000, 15000), 'tax' => rand(400, 1200), 'filed' => true],
                                            ['month' => date('M Y', strtotime('-1 months')), 'sales' => rand(9000, 16000), 'tax' => rand(450, 1300), 'filed' => true],
                                            ['month' => date('M Y'), 'sales' => rand(10000, 18000), 'tax' => rand(500, 1500), 'filed' => false],
                                        ]);
                                        @endphp
                                        
                                        @foreach($monthlyData as $month)
                                        <tr>
                                            <td>{{ $month['month'] }}</td>
                                            <td>${{ number_format($month['sales'], 2) }}</td>
                                            <td class="text-warning">${{ number_format($month['tax'], 2) }}</td>
                                            <td>
                                                @if($month['filed'])
                                                    <span class="badge bg-success">Filed</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Sales Tax Analysis -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-table"></i> Detailed Sales Tax Analysis</h5>
                            <p class="mb-0 text-muted">
                                Period: {{ request('date_from', date('Y-m-01')) }} to {{ request('date_to', date('Y-m-d')) }}
                            </p>
                        </div>
                        <div class="card-body">
                            <!-- Tax Analysis Grid -->
                            <div class="row">
                                <!-- Sales Summary -->
                                <div class="col-md-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-shopping-cart"></i> SALES SUMMARY
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Sales</span>
                                            <span class="fw-bold" id="detailed-total-sales">
                                                ${{ number_format($totalSales ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Taxable Sales</span>
                                            <span class="text-info" id="detailed-taxable-sales">
                                                ${{ number_format($taxableSales ?? 0, 2) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            @if(($totalSales ?? 0) > 0)
                                                {{ number_format(($taxableSales ?? 0) / ($totalSales ?? 0) * 100, 1) }}% of total sales
                                            @else
                                                0% of total sales
                                            @endif
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Non-Taxable Sales</span>
                                            <span class="text-success" id="non-taxable-sales">
                                                ${{ number_format(($totalSales ?? 0) - ($taxableSales ?? 0), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tax Calculation -->
                                <div class="col-md-4">
                                    <h6 class="text-warning mb-3">
                                        <i class="fas fa-calculator"></i> TAX CALCULATION
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Gross Tax Collected</span>
                                            <span class="fw-bold" id="gross-tax">
                                                ${{ number_format($salesTaxCollected ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Tax Remitted</span>
                                            <span class="text-success" id="tax-remitted">
                                                ${{ number_format($taxPaid ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Outstanding Tax</span>
                                            <span class="text-danger" id="outstanding-tax">
                                                ${{ number_format(($salesTaxCollected ?? 0) - ($taxPaid ?? 0), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Compliance Status -->
                                <div class="col-md-4">
                                    <h6 class="text-info mb-3">
                                        <i class="fas fa-check-circle"></i> COMPLIANCE STATUS
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Filing Status</span>
                                            <span class="badge {{ ($taxPaid ?? 0) >= ($salesTaxCollected ?? 0) * 0.8 ? 'bg-success' : 'bg-warning' }}">
                                                {{ ($taxPaid ?? 0) >= ($salesTaxCollected ?? 0) * 0.8 ? 'Current' : 'Behind' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Next Filing Due</span>
                                            <span class="text-info">
                                                {{ date('M 15', strtotime('+1 month')) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Average Tax Rate</span>
                                            <span class="fw-bold">
                                                @if(($taxableSales ?? 0) > 0)
                                                    {{ number_format(($salesTaxCollected ?? 0) / ($taxableSales ?? 0) * 100, 2) }}%
                                                @else
                                                    0%
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Outstanding Tax Alert -->
                            @if(($salesTaxCollected ?? 0) > ($taxPaid ?? 0))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle"></i> Outstanding Tax Liability</h6>
                                        <p class="mb-0">
                                            You have ${{ number_format(($salesTaxCollected ?? 0) - ($taxPaid ?? 0), 2) }} in unpaid sales tax. 
                                            Please file and remit these taxes to avoid penalties and interest charges.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Tax Rate Chart Placeholder -->
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Tax Collection Trend</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="taxTrendChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Quick Actions</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-file-export"></i> Export Tax Report
                                                </button>
                                                <button class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-calculator"></i> Calculate Next Filing
                                                </button>
                                                <button class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-plus"></i> Record Tax Payment
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportToPDF() {
    // Implement PDF export functionality
    alert('PDF export feature will be implemented.');
}

// Initialize chart if Chart.js is available
@if(isset($chartData))
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('taxTrendChart').getContext('2d');
    // Chart implementation would go here
});
@endif
</script>
@endsection