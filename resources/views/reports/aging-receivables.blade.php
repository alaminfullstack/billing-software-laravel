@extends('layouts.app')

@section('title', 'Aging Receivables Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-clock"></i> Aging Receivables Report</h1>
                <div>
                    <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            <!-- Report Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Aging Receivables Report</h5>
                            <p class="text-muted">As of {{ $asOfDate->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Currency:</strong> {{ $currency->name }} ({{ $currency->code }})</p>
                            <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals['current'], 2) }}</h3>
                            <p class="mb-0">Current</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals['1_30'], 2) }}</h3>
                            <p class="mb-0">1-30 Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totals['31_60'] + $totals['61_90'] + $totals['over_90'], 2) }}</h3>
                            <p class="mb-0">Over 30 Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format(array_sum($totals), 2) }}</h3>
                            <p class="mb-0">Total Outstanding</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aging Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Outstanding Invoices by Aging Period</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Days Overdue</th>
                                    <th>Aging Period</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                
                                <!-- Current -->
                                @foreach($agingData['current'] as $item)
                                <tr class="table-success">
                                    <td>{{ $item['invoice']->invoice_number }}</td>
                                    <td>{{ $item['customer']->name }}</td>
                                    <td>{{ $item['invoice']->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $item['invoice']->due_date->format('M d, Y') }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                    <td>{{ $item['days_overdue'] }}</td>
                                    <td><span class="badge badge-success">Current</span></td>
                                </tr>
                                @php $grandTotal += $item['amount']; @endphp
                                @endforeach
                                
                                <!-- 1-30 Days -->
                                @foreach($agingData['1_30'] as $item)
                                <tr class="table-warning">
                                    <td>{{ $item['invoice']->invoice_number }}</td>
                                    <td>{{ $item['customer']->name }}</td>
                                    <td>{{ $item['invoice']->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $item['invoice']->due_date->format('M d, Y') }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                    <td>{{ $item['days_overdue'] }}</td>
                                    <td><span class="badge badge-warning">1-30 Days</span></td>
                                </tr>
                                @php $grandTotal += $item['amount']; @endphp
                                @endforeach
                                
                                <!-- 31-60 Days -->
                                @foreach($agingData['31_60'] as $item)
                                <tr class="table-danger">
                                    <td>{{ $item['invoice']->invoice_number }}</td>
                                    <td>{{ $item['customer']->name }}</td>
                                    <td>{{ $item['invoice']->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $item['invoice']->due_date->format('M d, Y') }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                    <td>{{ $item['days_overdue'] }}</td>
                                    <td><span class="badge badge-danger">31-60 Days</span></td>
                                </tr>
                                @php $grandTotal += $item['amount']; @endphp
                                @endforeach
                                
                                <!-- 61-90 Days -->
                                @foreach($agingData['61_90'] as $item)
                                <tr class="table-danger">
                                    <td>{{ $item['invoice']->invoice_number }}</td>
                                    <td>{{ $item['customer']->name }}</td>
                                    <td>{{ $item['invoice']->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $item['invoice']->due_date->format('M d, Y') }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                    <td>{{ $item['days_overdue'] }}</td>
                                    <td><span class="badge badge-danger">61-90 Days</span></td>
                                </tr>
                                @php $grandTotal += $item['amount']; @endphp
                                @endforeach
                                
                                <!-- Over 90 Days -->
                                @foreach($agingData['over_90'] as $item)
                                <tr class="table-danger">
                                    <td>{{ $item['invoice']->invoice_number }}</td>
                                    <td>{{ $item['customer']->name }}</td>
                                    <td>{{ $item['invoice']->issue_date->format('M d, Y') }}</td>
                                    <td>{{ $item['invoice']->due_date->format('M d, Y') }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                    <td>{{ $item['days_overdue'] }}</td>
                                    <td><span class="badge badge-danger">Over 90 Days</span></td>
                                </tr>
                                @php $grandTotal += $item['amount']; @endphp
                                @endforeach
                                
                                @if($grandTotal == 0)
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No outstanding receivables</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <td colspan="4"><strong>TOTAL OUTSTANDING</strong></td>
                                    <td class="text-end"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Analysis -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-pie"></i> Aging Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                $totalOverdue = $totals['1_30'] + $totals['31_60'] + $totals['61_90'] + $totals['over_90'];
                                $currentPercent = $grandTotal > 0 ? ($totals['current'] / $grandTotal) * 100 : 0;
                                $overduePercent = $grandTotal > 0 ? ($totalOverdue / $grandTotal) * 100 : 0;
                                @endphp
                                
                                @if($totals['current'] > 0)
                                <div class="progress-bar bg-success" style="width: {{ $currentPercent }}%" 
                                     title="Current: {{ number_format($currentPercent, 1) }}%">
                                    {{ number_format($currentPercent, 0) }}%
                                </div>
                                @endif
                                
                                @if($totalOverdue > 0)
                                <div class="progress-bar bg-danger" style="width: {{ $overduePercent }}%" 
                                     title="Overdue: {{ number_format($overduePercent, 1) }}%">
                                    {{ number_format($overduePercent, 0) }}%
                                </div>
                                @endif
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-success">Current</small>
                                    <div class="fw-bold">{{ number_format($currentPercent, 1) }}%</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-danger">Overdue</small>
                                    <div class="fw-bold">{{ number_format($overduePercent, 1) }}%</div>
                                </div>
                            </div>
                            
                            @if($overduePercent > 20)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>High Overdue Percentage</strong><br>
                                    {{ number_format($overduePercent, 1) }}% of receivables are overdue. Consider implementing stricter payment terms or follow-up procedures.
                                </div>
                            @elseif($overduePercent > 10)
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Moderate Overdue Amount</strong><br>
                                    {{ number_format($overduePercent, 1) }}% of receivables are overdue. Monitor these accounts closely.
                                </div>
                            @else
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Good Collection Rate</strong><br>
                                    Only {{ number_format($overduePercent, 1) }}% of receivables are overdue. Excellent payment collection performance.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-lightbulb"></i> Collection Recommendations</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                @if($totals['over_90'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation text-danger"></i> 
                                        <strong>Over 90 Days:</strong> Immediately contact customers and consider account restrictions.
                                    </li>
                                @endif
                                
                                @if($totals['61_90'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning"></i> 
                                        <strong>61-90 Days:</strong> Send final reminder notices and implement late fees.
                                    </li>
                                @endif
                                
                                @if($totals['31_60'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-phone text-info"></i> 
                                        <strong>31-60 Days:</strong> Make personal contact to discuss payment arrangements.
                                    </li>
                                @endif
                                
                                @if($totals['1_30'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-envelope text-primary"></i> 
                                        <strong>1-30 Days:</strong> Send friendly payment reminder emails.
                                    </li>
                                @endif
                                
                                <li class="mb-2">
                                    <i class="fas fa-cog text-muted"></i> 
                                    <strong>Process Improvement:</strong> Consider requiring deposits or shorter payment terms for repeat late payers.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
@media print {
    .btn, .nav, .sidebar { display: none !important; }
    .card { border: 1px solid #000 !important; }
    .card-header { background-color: #f5f5f5 !important; }
}
</style>
@endsection