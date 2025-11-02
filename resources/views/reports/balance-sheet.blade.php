@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-balance-scale"></i> Balance Sheet</h1>
                <div>
                    <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportPDF()">
                        <i class="fas fa-download"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Report Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Balance Sheet</h5>
                            <p class="text-muted">As of {{ \Carbon\Carbon::parse($asOfDate)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i') }}</p>
                            <p><strong>System Currency:</strong> USD</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Sheet Content -->
            <div class="row">
                <!-- Assets -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-building"></i> ASSETS</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted">Current Assets</h6>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Cash and Bank Accounts</span>
                                    <span class="fw-bold">${{ number_format($cashAndBank, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Accounts Receivable</span>
                                    <span class="fw-bold">${{ number_format($accountsReceivable, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Prepaid Expenses</span>
                                    <span class="fw-bold">${{ number_format($prepaidExpenses, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted">Fixed Assets</h6>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Office Equipment</span>
                                    <span class="fw-bold">${{ number_format($officeEquipment, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL ASSETS</strong>
                                    <strong class="text-primary">${{ number_format($totalAssets, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liabilities & Equity -->
                <div class="col-md-6">
                    <!-- Liabilities -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-credit-card"></i> LIABILITIES</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted">Current Liabilities</h6>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Accounts Payable</span>
                                    <span class="fw-bold">${{ number_format($accountsPayable, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Accrued Expenses</span>
                                    <span class="fw-bold">${{ number_format($accruedExpenses, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL LIABILITIES</strong>
                                    <strong class="text-warning">${{ number_format($totalLiabilities, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Equity -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-chart-line"></i> EQUITY</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>Owner's Equity</span>
                                <span class="fw-bold">${{ number_format($ownerEquity, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>Retained Earnings</span>
                                <span class="fw-bold">${{ number_format($retainedEarnings, 2) }}</span>
                            </div>
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL EQUITY</strong>
                                    <strong class="text-success">${{ number_format($totalEquity, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Check -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h6>TOTAL ASSETS</h6>
                                    <h4 class="text-primary">${{ number_format($totalAssets, 2) }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>TOTAL LIABILITIES + EQUITY</h6>
                                    <h4 class="text-success">${{ number_format($totalLiabilities + $totalEquity, 2) }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>BALANCE CHECK</h6>
                                    @php
                                        $difference = $totalAssets - ($totalLiabilities + $totalEquity);
                                        $balanceClass = abs($difference) < 0.01 ? 'text-success' : 'text-danger';
                                    @endphp
                                    <h4 class="{{ $balanceClass }}">
                                        {{ abs($difference) < 0.01 ? '✓ Balanced' : '✗ Out of Balance' }}
                                    </h4>
                                    <small class="text-muted">Difference: ${{ number_format($difference, 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Ratios -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-calculator"></i> Key Financial Ratios</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h6>Debt-to-Asset Ratio</h6>
                                    <h5>{{ $totalAssets > 0 ? number_format($totalLiabilities / $totalAssets * 100, 1) : 0 }}%</h5>
                                    <small class="text-muted">Total Liabilities ÷ Total Assets</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6>Equity Ratio</h6>
                                    <h5>{{ $totalAssets > 0 ? number_format($totalEquity / $totalAssets * 100, 1) : 0 }}%</h5>
                                    <small class="text-muted">Total Equity ÷ Total Assets</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6>Financial Leverage</h6>
                                    <h5>{{ $totalEquity > 0 ? number_format($totalAssets / $totalEquity, 2) : 0 }}x</h5>
                                    <small class="text-muted">Total Assets ÷ Total Equity</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportPDF() {
    // This would integrate with a PDF library
    alert('PDF export functionality would be implemented here');
    // Example: window.location.href = '/financial-reports/balance-sheet/pdf?as_of_date={{ $asOfDate }}';
}
</script>
@endsection