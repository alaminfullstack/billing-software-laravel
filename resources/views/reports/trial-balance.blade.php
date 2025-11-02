@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-balance-scale"></i> Trial Balance</h1>
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
                            <h5>Trial Balance</h5>
                            <p class="text-muted">As of {{ $asOfDate->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trial Balance Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Trial Balance - {{ $asOfDate->format('F d, Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Account Type</th>
                                    <th class="text-end">Debit Balance</th>
                                    <th class="text-end">Credit Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalDebits = 0; $totalCredits = 0; @endphp
                                
                                @foreach($trialBalance->sortBy('account.code') as $item)
                                <tr>
                                    <td>{{ $item['account']->formatted_code }}</td>
                                    <td>{{ $item['account']->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($item['account']->type == 'asset') bg-primary
                                            @elseif($item['account']->type == 'liability') bg-warning
                                            @elseif($item['account']->type == 'equity') bg-success
                                            @elseif($item['account']->type == 'revenue') bg-info
                                            @else bg-danger @endif">
                                            {{ $item['account']->account_type_name }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($item['account']->normal_balance == 'debit' && $item['base_currency_debit'] > 0)
                                            {{ number_format($item['base_currency_debit'], 2) }}
                                            @php $totalDebits += $item['base_currency_debit']; @endphp
                                        @elseif($item['account']->normal_balance == 'credit' && $item['base_currency_credit'] > 0)
                                            {{ number_format($item['base_currency_credit'], 2) }}
                                            @php $totalCredits += $item['base_currency_credit']; @endphp
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item['account']->normal_balance == 'credit' && $item['base_currency_credit'] > 0)
                                            {{ number_format($item['base_currency_credit'], 2) }}
                                            @php $totalCredits += $item['base_currency_credit']; @endphp
                                        @elseif($item['account']->normal_balance == 'debit' && $item['base_currency_debit'] > 0)
                                            {{ number_format($item['base_currency_debit'], 2) }}
                                            @php $totalDebits += $item['base_currency_debit']; @endphp
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                
                                <!-- Total Row -->
                                <tr class="table-primary">
                                    <td colspan="3"><strong>TOTALS</strong></td>
                                    <td class="text-end"><strong>{{ number_format($totalDebits, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($totalCredits, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Balance Check -->
                    <div class="alert {{ abs($totalDebits - $totalCredits) < 0.01 ? 'alert-success' : 'alert-danger' }} mt-3">
                        <strong>Balance Check:</strong> 
                        Total Debits ({{ number_format($totalDebits, 2) }}) 
                        {{ abs($totalDebits - $totalCredits) < 0.01 ? '=' : '≠' }} 
                        Total Credits ({{ number_format($totalCredits, 2) }})
                        
                        @if(abs($totalDebits - $totalCredits) >= 0.01)
                            <br><small>Difference: {{ number_format(abs($totalDebits - $totalCredits), 2) }}</small>
                            <br><small class="text-danger">This difference indicates an accounting error that needs to be corrected.</small>
                        @else
                            <br><small class="text-success">Trial balance is in balance. Accounting records are mathematically correct.</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Summary by Type -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-bar"></i> Account Summary by Type</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Account Type</th>
                                            <th class="text-end">Number of Accounts</th>
                                            <th class="text-end">Total Debits</th>
                                            <th class="text-end">Total Credits</th>
                                            <th class="text-end">Net Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $typeSummary = [];
                                        foreach($trialBalance as $item) {
                                            $type = $item['account']->type;
                                            if (!isset($typeSummary[$type])) {
                                                $typeSummary[$type] = [
                                                    'count' => 0,
                                                    'debits' => 0,
                                                    'credits' => 0,
                                                    'name' => $item['account']->account_type_name
                                                ];
                                            }
                                            $typeSummary[$type]['count']++;
                                            
                                            if ($item['account']->normal_balance == 'debit') {
                                                $typeSummary[$type]['debits'] += $item['base_currency_debit'];
                                                $typeSummary[$type]['credits'] += $item['base_currency_credit'];
                                            } else {
                                                $typeSummary[$type]['debits'] += $item['base_currency_debit'];
                                                $typeSummary[$type]['credits'] += $item['base_currency_credit'];
                                            }
                                        }
                                        @endphp
                                        
                                        @foreach($typeSummary as $type => $summary)
                                        <tr>
                                            <td>{{ $summary['name'] }}</td>
                                            <td class="text-end">{{ $summary['count'] }}</td>
                                            <td class="text-end">{{ number_format($summary['debits'], 2) }}</td>
                                            <td class="text-end">{{ number_format($summary['credits'], 2) }}</td>
                                            <td class="text-end">
                                                {{ number_format(abs($summary['debits'] - $summary['credits']), 2) }}
                                                @if($summary['debits'] > $summary['credits'])
                                                    <small class="text-primary">(Debit)</small>
                                                @elseif($summary['credits'] > $summary['debits'])
                                                    <small class="text-warning">(Credit)</small>
                                                @else
                                                    <small class="text-muted">(Balanced)</small>
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

            <!-- Quick Analysis -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-lightbulb"></i> Quick Analysis</h6>
                        </div>
                        <div class="card-body">
                            @if(abs($totalDebits - $totalCredits) < 0.01)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Trial Balance is Balanced</strong>
                                    <p class="mb-0">All debit and credit totals match, indicating mathematical accuracy in the accounting records.</p>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> 
                                    <strong>Trial Balance is Unbalanced</strong>
                                    <p class="mb-0">There's a difference of {{ number_format(abs($totalDebits - $totalCredits), 2) }} between debits and credits. Check for missing or incorrect entries.</p>
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <h6>Account Distribution</h6>
                                @foreach($typeSummary as $type => $summary)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $summary['name'] }}:</span>
                                        <span>{{ $summary['count'] }} accounts</span>
                                    </div>
                                @endforeach
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
                            <p><strong>Total Accounts:</strong> {{ $trialBalance->count() }}</p>
                            <p><strong>Base Currency:</strong> {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
                            <p><strong>Report Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            
                            <div class="mt-3">
                                <h6>Normal Balance Rules</h6>
                                <ul class="list-unstyled small">
                                    <li><span class="badge bg-primary">Assets</span> - Debit</li>
                                    <li><span class="badge bg-warning">Liabilities</span> - Credit</li>
                                    <li><span class="badge bg-success">Equity</span> - Credit</li>
                                    <li><span class="badge bg-info">Revenue</span> - Credit</li>
                                    <li><span class="badge bg-danger">Expenses</span> - Debit</li>
                                </ul>
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
    .table { font-size: 12px; }
}
</style>
@endsection