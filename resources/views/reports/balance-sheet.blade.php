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
                            <p class="text-muted">As of {{ $asOfDate->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
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
                            @php $totalAssets = 0; @endphp
                            
                            @foreach($balanceSheet['assets']->groupBy('account.category') as $category => $accounts)
                                <div class="mb-3">
                                    <h6 class="text-muted">{{ $category }}</h6>
                                    @foreach($accounts as $item)
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <span>{{ $item['account']->name }} ({{ $item['account']->code }})</span>
                                            <span class="fw-bold">{{ number_format(abs($item['balance']), 2) }}</span>
                                        </div>
                                        @php $totalAssets += $item['balance']; @endphp
                                    @endforeach
                                </div>
                            @endforeach
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL ASSETS</strong>
                                    <strong class="text-primary">{{ number_format($totalAssets, 2) }}</strong>
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
                            @php $totalLiabilities = 0; @endphp
                            
                            @foreach($balanceSheet['liabilities']->groupBy('account.category') as $category => $accounts)
                                <div class="mb-3">
                                    <h6 class="text-muted">{{ $category }}</h6>
                                    @foreach($accounts as $item)
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <span>{{ $item['account']->name }} ({{ $item['account']->code }})</span>
                                            <span class="fw-bold">{{ number_format(abs($item['balance']), 2) }}</span>
                                        </div>
                                        @php $totalLiabilities += $item['balance']; @endphp
                                    @endforeach
                                </div>
                            @endforeach
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL LIABILITIES</strong>
                                    <strong class="text-warning">{{ number_format($totalLiabilities, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Equity -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-chart-pie"></i> EQUITY</h5>
                        </div>
                        <div class="card-body">
                            @php $totalEquity = 0; @endphp
                            
                            @foreach($balanceSheet['equity']->groupBy('account.category') as $category => $accounts)
                                <div class="mb-3">
                                    <h6 class="text-muted">{{ $category }}</h6>
                                    @foreach($accounts as $item)
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <span>{{ $item['account']->name }} ({{ $item['account']->code }})</span>
                                            <span class="fw-bold">{{ number_format(abs($item['balance']), 2) }}</span>
                                        </div>
                                        @php $totalEquity += $item['balance']; @endphp
                                    @endforeach
                                </div>
                            @endforeach
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL EQUITY</strong>
                                    <strong class="text-success">{{ number_format($totalEquity, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h3 class="text-primary">{{ number_format($totalAssets, 2) }}</h3>
                            <p class="text-muted">Total Assets</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="text-warning">{{ number_format($totalLiabilities, 2) }}</h3>
                            <p class="text-muted">Total Liabilities</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="text-success">{{ number_format($totalEquity, 2) }}</h3>
                            <p class="text-muted">Total Equity</p>
                        </div>
                    </div>
                    
                    <!-- Balance Check -->
                    <div class="alert {{ (abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01) ? 'alert-success' : 'alert-danger' }} mt-3">
                        <strong>Balance Check:</strong> 
                        Assets ({{ number_format($totalAssets, 2) }}) 
                        {{ (abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01) ? '=' : '≠' }} 
                        Liabilities + Equity ({{ number_format($totalLiabilities + $totalEquity, 2) }})
                        @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) >= 0.01)
                            <br><small>Difference: {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Ratios -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-calculator"></i> Key Financial Ratios</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-info">
                                            {{ $totalLiabilities > 0 ? number_format(($totalLiabilities / $totalAssets) * 100, 1) : 0 }}%
                                        </h4>
                                        <small class="text-muted">Debt to Asset Ratio</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-primary">
                                            {{ $totalAssets > 0 ? number_format(($totalEquity / $totalAssets) * 100, 1) : 0 }}%
                                        </h4>
                                        <small class="text-muted">Equity to Asset Ratio</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle"></i> Report Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i:s') }}</p>
                            <p><strong>As of Date:</strong> {{ $asOfDate->format('F d, Y') }}</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
                            <p><strong>Report Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
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
function exportPDF() {
    const url = new URL(window.location);
    url.searchParams.set('export', 'pdf');
    window.open(url.toString(), '_blank');
}

// Print specific styles
window.onbeforeprint = function() {
    document.querySelector('.btn').style.display = 'none';
};

window.onafterprint = function() {
    document.querySelector('.btn').style.display = '';
};
</script>

<style>
@media print {
    .btn, .nav, .sidebar { display: none !important; }
    .card { border: 1px solid #000 !important; }
    .card-header { background-color: #f5f5f5 !important; }
}
</style>
@endsection