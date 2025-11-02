@extends('layouts.app')

@section('title', 'Cash Flow Statement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-money-bill-wave"></i> Cash Flow Statement</h1>
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
                            <h5>Cash Flow Statement</h5>
                            <p class="text-muted">For the period {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Activities -->
            <div class="row">
                <!-- Operating Activities -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-cogs"></i> CASH FLOWS FROM OPERATING ACTIVITIES</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        @foreach($cashFlow['operating_activities']['transactions'] as $transaction)
                                        <tr>
                                            <td class="border-0">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                            <td class="border-0">{{ $transaction->description }}</td>
                                            <td class="text-end border-0">
                                                <span class="{{ $transaction->base_currency_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($transaction->base_currency_amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-primary">
                                            <td colspan="2"><strong>Net Cash from Operating Activities</strong></td>
                                            <td class="text-end"><strong>{{ number_format($cashFlow['operating_activities']['amount'], 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investing Activities -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-chart-line"></i> CASH FLOWS FROM INVESTING ACTIVITIES</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        @forelse($cashFlow['investing_activities']['transactions'] as $transaction)
                                        <tr>
                                            <td class="border-0">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                            <td class="border-0">{{ $transaction->description }}</td>
                                            <td class="text-end border-0">
                                                <span class="{{ $transaction->base_currency_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($transaction->base_currency_amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No investing activities during this period</td>
                                        </tr>
                                        @endforelse
                                        <tr class="table-warning">
                                            <td colspan="2"><strong>Net Cash from Investing Activities</strong></td>
                                            <td class="text-end"><strong>{{ number_format($cashFlow['investing_activities']['amount'], 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financing Activities -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-university"></i> CASH FLOWS FROM FINANCING ACTIVITIES</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        @forelse($cashFlow['financing_activities']['transactions'] as $transaction)
                                        <tr>
                                            <td class="border-0">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                            <td class="border-0">{{ $transaction->description }}</td>
                                            <td class="text-end border-0">
                                                <span class="{{ $transaction->base_currency_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($transaction->base_currency_amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No financing activities during this period</td>
                                        </tr>
                                        @endforelse
                                        <tr class="table-success">
                                            <td colspan="2"><strong>Net Cash from Financing Activities</strong></td>
                                            <td class="text-end"><strong>{{ number_format($cashFlow['financing_activities']['amount'], 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5><i class="fas fa-calculator"></i> NET INCREASE (DECREASE) IN CASH</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-primary">{{ number_format($cashFlow['operating_activities']['amount'], 2) }}</h4>
                                <p class="text-muted">Operating Activities</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($cashFlow['investing_activities']['amount'], 2) }}</h4>
                                <p class="text-muted">Investing Activities</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($cashFlow['financing_activities']['amount'], 2) }}</h4>
                                <p class="text-muted">Financing Activities</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="{{ $cashFlow['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($cashFlow['net_cash_flow'], 2) }}
                            </h3>
                            <p class="text-muted">Net Increase (Decrease) in Cash</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Analysis -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-pie"></i> Cash Flow Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                $operatingPercent = $cashFlow['net_cash_flow'] != 0 ? ($cashFlow['operating_activities']['amount'] / $cashFlow['net_cash_flow']) * 100 : 0;
                                $investingPercent = $cashFlow['net_cash_flow'] != 0 ? ($cashFlow['investing_activities']['amount'] / $cashFlow['net_cash_flow']) * 100 : 0;
                                $financingPercent = $cashFlow['net_cash_flow'] != 0 ? ($cashFlow['financing_activities']['amount'] / $cashFlow['net_cash_flow']) * 100 : 0;
                                @endphp
                                
                                @if($operatingPercent > 0)
                                <div class="progress-bar bg-primary" style="width: {{ abs($operatingPercent) }}%" 
                                     title="Operating: {{ number_format($operatingPercent, 1) }}%">
                                    {{ number_format($operatingPercent, 0) }}%
                                </div>
                                @endif
                                
                                @if($investingPercent > 0)
                                <div class="progress-bar bg-warning text-dark" style="width: {{ abs($investingPercent) }}%" 
                                     title="Investing: {{ number_format($investingPercent, 1) }}%">
                                    {{ number_format($investingPercent, 0) }}%
                                </div>
                                @endif
                                
                                @if($financingPercent > 0)
                                <div class="progress-bar bg-success" style="width: {{ abs($financingPercent) }}%" 
                                     title="Financing: {{ number_format($financingPercent, 1) }}%">
                                    {{ number_format($financingPercent, 0) }}%
                                </div>
                                @endif
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-primary">Operating</small>
                                    <div class="fw-bold">{{ number_format($operatingPercent, 1) }}%</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-warning">Investing</small>
                                    <div class="fw-bold">{{ number_format($investingPercent, 1) }}%</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-success">Financing</small>
                                    <div class="fw-bold">{{ number_format($financingPercent, 1) }}%</div>
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
                            <p><strong>Period:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
                            <p><strong>Report Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            
                            @if($cashFlow['net_cash_flow'] > 0)
                                <div class="alert alert-success">
                                    <i class="fas fa-arrow-up"></i> Positive cash flow indicates healthy liquidity
                                </div>
                            @elseif($cashFlow['net_cash_flow'] < 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-arrow-down"></i> Negative cash flow requires attention
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-minus"></i> Neutral cash flow for the period
                                </div>
                            @endif
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