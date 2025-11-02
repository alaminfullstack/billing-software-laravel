@extends('layouts.app')

@section('title', 'Tax Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-calculator"></i> Tax Report</h1>
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
                            <h5>Tax Report</h5>
                            <p class="text-muted">Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
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
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($salesTaxCollected, 2) }}</h3>
                            <p class="mb-0">Sales Tax Collected</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($taxDeductibleAmount, 2) }}</h3>
                            <p class="mb-0">Tax Deductible Expenses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($salesTaxCollected - $taxDeductibleAmount, 2) }}</h3>
                            <p class="mb-0">Net Tax Position</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Tax Collected -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-receipt"></i> Sales Tax Collected</h5>
                </div>
                <div class="card-body">
                    @if($invoicesWithTax->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Issue Date</th>
                                        <th>Subtotal</th>
                                        <th>Tax Amount</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoicesWithTax as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->customer->name }}</td>
                                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                        <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                                        <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-info">
                                    <tr>
                                        <td colspan="4"><strong>TOTAL TAX COLLECTED</strong></td>
                                        <td class="text-end"><strong>{{ number_format($salesTaxCollected, 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No sales tax collected during this period</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tax Deductible Expenses -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-truck"></i> Tax Deductible Expenses</h5>
                </div>
                <div class="card-body">
                    @if($taxDeductibleExpenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxDeductibleExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>{{ $expense->category->name ?? 'Uncategorized' }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>{{ $expense->vendor ?: ($expense->supplier->name ?? 'N/A') }}</td>
                                        <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <td colspan="4"><strong>TOTAL TAX DEDUCTIBLE EXPENSES</strong></td>
                                        <td class="text-end"><strong>{{ number_format($taxDeductibleAmount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No tax deductible expenses during this period</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expenses by Category -->
            @if($taxByCategory->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar"></i> Tax Deductible Expenses by Category</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Percentage</th>
                                    <th style="width: 200px;">Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxByCategory as $category => $amount)
                                <tr>
                                    <td>{{ $category }}</td>
                                    <td class="text-end">{{ number_format($amount, 2) }}</td>
                                    <td class="text-end">{{ number_format(($amount / $taxDeductibleAmount) * 100, 1) }}%</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ ($amount / $taxDeductibleAmount) * 100 }}%">
                                                {{ number_format(($amount / $taxDeductibleAmount) * 100, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tax Summary -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clipboard-list"></i> Tax Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Sales Tax Collected:</strong></td>
                                        <td class="text-end">{{ number_format($salesTaxCollected, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tax Deductible Expenses:</strong></td>
                                        <td class="text-end">{{ number_format($taxDeductibleAmount, 2) }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Net Tax Position:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($salesTaxCollected - $taxDeductibleAmount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="alert {{ ($salesTaxCollected - $taxDeductibleAmount) >= 0 ? 'alert-info' : 'alert-success' }}">
                                <h6><i class="fas {{ ($salesTaxCollected - $taxDeductibleAmount) >= 0 ? 'fa-info-circle' : 'fa-check-circle' }}"></i> 
                                Tax Analysis</h6>
                                
                                @if($salesTaxCollected > $taxDeductibleAmount)
                                    <p><strong>Tax Due:</strong> You have collected more sales tax than your deductible expenses. You may owe additional taxes.</p>
                                    <p><small class="text-muted">Consider reviewing your tax obligations and making estimated payments if required.</small></p>
                                @else
                                    <p><strong>Tax Refund Potential:</strong> Your deductible expenses exceed sales tax collected. You may be eligible for a tax refund or credit.</p>
                                    <p><small class="text-muted">Keep detailed records of all deductible expenses for tax filing.</small></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-lightbulb"></i> Tax Recommendations</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-receipt text-primary"></i> 
                                    <strong>Record Keeping:</strong> Maintain detailed records of all sales tax collections and remittances.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-info"></i> 
                                    <strong>Filing Deadlines:</strong> Ensure timely filing of sales tax returns to avoid penalties.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-file-alt text-success"></i> 
                                    <strong>Documentation:</strong> Keep receipts and documentation for all tax deductible expenses.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calculator text-warning"></i> 
                                    <strong>Regular Reviews:</strong> Review tax positions regularly with a qualified tax professional.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-sync text-danger"></i> 
                                    <strong>Quarterly Reports:</strong> Generate quarterly tax reports for better financial planning.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle"></i> Important Notes</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning"></i> 
                                    This report is for informational purposes only and should not be considered as tax advice.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-user-tie text-primary"></i> 
                                    Consult with a qualified tax professional for specific tax situations and requirements.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-info"></i> 
                                    Tax laws and regulations may vary by jurisdiction and change over time.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-shield-alt text-success"></i> 
                                    Ensure compliance with local, state, and federal tax obligations.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-database text-danger"></i> 
                                    Maintain accurate records for at least the required retention period.
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