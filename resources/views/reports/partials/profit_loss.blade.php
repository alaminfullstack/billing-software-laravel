@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-chart-line"></i> Profit & Loss Statement</h1>
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
                    <form method="GET" action="{{ route('reports.profit-loss') }}" class="row g-3">
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

            <!-- Report Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="fas fa-arrow-up"></i> Total Revenue
                            </h5>
                            <h3 class="text-success" id="total-revenue">
                                ${{ number_format($revenue ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Income generated</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="fas fa-arrow-down"></i> Total Expenses
                            </h5>
                            <h3 class="text-danger" id="total-expenses">
                                ${{ number_format($expenses ?? 0, 2) }}
                            </h3>
                            <small class="text-muted">Costs incurred</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">
                                <i class="fas fa-chart-pie"></i> Gross Profit
                            </h5>
                            <h3 class="text-info" id="gross-profit">
                                ${{ number_format(($revenue ?? 0) - ($expenses ?? 0), 2) }}
                            </h3>
                            <small class="text-muted">Revenue minus COGS</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title {{ ($revenue ?? 0) - ($expenses ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-chart-line"></i> Net Profit
                            </h5>
                            <h3 class="{{ ($revenue ?? 0) - ($expenses ?? 0) >= 0 ? 'text-success' : 'text-danger' }}" id="net-profit">
                                ${{ number_format(($revenue ?? 0) - ($expenses ?? 0), 2) }}
                            </h3>
                            <small class="text-muted">{{ ($revenue ?? 0) - ($expenses ?? 0) >= 0 ? 'Profit' : 'Loss' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed P&L Statement -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Detailed Profit & Loss Statement</h5>
                    <p class="mb-0 text-muted">
                        Period: {{ request('date_from', date('Y-m-01')) }} to {{ request('date_to', date('Y-m-d')) }}
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Revenue Section -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-arrow-up"></i> REVENUE
                            </h6>
                            
                            <!-- Sales Revenue -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Sales Revenue</strong></span>
                                    <span class="text-success" id="sales-revenue">
                                        ${{ number_format($salesRevenue ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Revenue from product sales</small>
                            </div>

                            <!-- Service Revenue -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Service Revenue</strong></span>
                                    <span class="text-success" id="service-revenue">
                                        ${{ number_format($serviceRevenue ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Revenue from services rendered</small>
                            </div>

                            <!-- Other Income -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Other Income</strong></span>
                                    <span class="text-success" id="other-income">
                                        ${{ number_format($otherIncome ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Interest, dividends, etc.</small>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <span><strong>Total Revenue</strong></span>
                                <span class="text-success fw-bold" id="total-revenue-detail">
                                    ${{ number_format($revenue ?? 0, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Expenses Section -->
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-arrow-down"></i> EXPENSES
                            </h6>
                            
                            <!-- Cost of Goods Sold -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Cost of Goods Sold</strong></span>
                                    <span class="text-danger" id="cogs">
                                        ${{ number_format($cogs ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Direct costs of producing goods/services</small>
                            </div>

                            <!-- Operating Expenses -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Operating Expenses</strong></span>
                                    <span class="text-danger" id="operating-expenses">
                                        ${{ number_format($operatingExpenses ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Rent, utilities, salaries, etc.</small>
                            </div>

                            <!-- Marketing & Sales -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Marketing & Sales</strong></span>
                                    <span class="text-danger" id="marketing-expenses">
                                        ${{ number_format($marketingExpenses ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Advertising, promotion costs</small>
                            </div>

                            <!-- Administrative Expenses -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Administrative</strong></span>
                                    <span class="text-danger" id="admin-expenses">
                                        ${{ number_format($adminExpenses ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Office supplies, software, etc.</small>
                            </div>

                            <!-- Interest & Taxes -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Interest & Other</strong></span>
                                    <span class="text-danger" id="interest-expenses">
                                        ${{ number_format($interestExpenses ?? 0, 2) }}
                                    </span>
                                </div>
                                <small class="text-muted">Interest, bank charges, etc.</small>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <span><strong>Total Expenses</strong></span>
                                <span class="text-danger fw-bold" id="total-expenses-detail">
                                    ${{ number_format($expenses ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Net Profit/Loss -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert {{ ($revenue ?? 0) - ($expenses ?? 0) >= 0 ? 'alert-success' : 'alert-danger' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        {{ ($revenue ?? 0) - ($expenses ?? 0) >= 0 ? 'NET PROFIT' : 'NET LOSS' }}
                                    </h4>
                                    <h3 class="mb-0">
                                        ${{ number_format(($revenue ?? 0) - ($expenses ?? 0), 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profitability Metrics -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="text-muted">Gross Profit Margin</h6>
                                    <h4 class="{{ ($revenue ?? 0) > 0 ? 'text-primary' : 'text-muted' }}">
                                        @if(($revenue ?? 0) > 0)
                                            {{ number_format((($revenue ?? 0) - ($cogs ?? 0)) / ($revenue ?? 0) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="text-muted">Net Profit Margin</h6>
                                    <h4 class="{{ ($revenue ?? 0) > 0 ? (($revenue ?? 0) - ($expenses ?? 0)) >= 0 ? 'text-success' : 'text-danger' : 'text-muted' }}">
                                        @if(($revenue ?? 0) > 0)
                                            {{ number_format((($revenue ?? 0) - ($expenses ?? 0)) / ($revenue ?? 0) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="text-muted">Revenue Growth</h6>
                                    <h4 class="text-info">-</h4>
                                    <small class="text-muted">vs. previous period</small>
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

// Update data if needed for dynamic updates
@if(isset($chartData))
// Chart.js implementation for visual representation
@endif
</script>
@endsection