@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit"></i> Edit Payment #{{ $payment->payment_number ?? 'N/A' }}</h1>
                <div>
                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> View Payment
                    </a>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Payments
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.update', $payment) }}" id="paymentForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Payment Details -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-money-bill"></i> Payment Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="invoice_id" class="form-label">Invoice <span class="text-danger">*</span></label>
                                            <select name="invoice_id" id="invoice_id" class="form-select">
                                                <option value="">Select an invoice</option>
                                                @foreach($invoices ?? [] as $invoice)
                                                    @php
                                                        $balance = $invoice->total - ($invoice->paid_amount ?? 0);
                                                    @endphp
                                                    <option value="{{ $invoice->id }}" 
                                                            data-balance="{{ $balance }}"
                                                            {{ ($payment->invoice_id == $invoice->id) ? 'selected' : '' }}>
                                                        {{ $invoice->invoice_number }} - {{ $invoice->customer->name ?? 'N/A' }} 
                                                        (Balance: ${{ number_format($balance, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_id" class="form-label">Customer</label>
                                            <select name="customer_id" id="customer_id" class="form-select">
                                                <option value="">Select a customer (optional)</option>
                                                @foreach($customers ?? [] as $customer)
                                                    <option value="{{ $customer->id }}" 
                                                            {{ ($payment->customer_id == $customer->id) ? 'selected' : '' }}>
                                                        {{ $customer->name }} ({{ $customer->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" id="payment_date" 
                                                   class="form-control" 
                                                   value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                            <input type="number" name="amount" id="amount" 
                                                   class="form-control" 
                                                   value="{{ old('amount', $payment->amount) }}" 
                                                   min="0.01" step="0.01" required>
                                            <div class="form-text" id="amount-help">
                                                Enter the payment amount
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                            <select name="payment_method" id="payment_method" class="form-select" required>
                                                <option value="">Select payment method</option>
                                                <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="check" {{ $payment->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="credit_card" {{ $payment->payment_method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="bank_transfer" {{ $payment->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="paypal" {{ $payment->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="stripe" {{ $payment->payment_method == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label">Reference Number</label>
                                            <input type="text" name="reference_number" id="reference_number" 
                                                   class="form-control" 
                                                   value="{{ old('reference_number', $payment->reference_number) }}" 
                                                   placeholder="Check number, transaction ID, etc.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_number" class="form-label">Payment Number</label>
                                            <input type="text" name="payment_number" id="payment_number" 
                                                   class="form-control" 
                                                   value="{{ old('payment_number', $payment->payment_number) }}" 
                                                   placeholder="Auto-generated if empty">
                                            <div class="form-text">Leave empty to auto-generate</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" 
                                              placeholder="Add any notes about this payment...">{{ old('notes', $payment->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details (for specific payment methods) -->
                        <div class="card mt-4" id="payment-method-details" 
                             style="display: {{ in_array($payment->payment_method, ['credit_card', 'stripe', 'check', 'bank_transfer']) ? 'block' : 'none' }};">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Additional Details</h5>
                            </div>
                            <div class="card-body">
                                <div id="credit-card-details" 
                                     style="display: {{ in_array($payment->payment_method, ['credit_card', 'stripe']) ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                                <input type="text" name="transaction_id" id="transaction_id" 
                                                       class="form-control" 
                                                       value="{{ old('transaction_id', $payment->transaction_id ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="processing_fee" class="form-label">Processing Fee</label>
                                                <input type="number" name="processing_fee" id="processing_fee" 
                                                       class="form-control" 
                                                       value="{{ old('processing_fee', $payment->processing_fee ?? 0) }}" 
                                                       min="0" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="check-details" 
                                     style="display: {{ $payment->payment_method == 'check' ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="check_number" class="form-label">Check Number</label>
                                                <input type="text" name="check_number" id="check_number" 
                                                       class="form-control" 
                                                       value="{{ old('check_number', $payment->check_number ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_name" class="form-label">Bank Name</label>
                                                <input type="text" name="bank_name" id="bank_name" 
                                                       class="form-control" 
                                                       value="{{ old('bank_name', $payment->bank_name ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="bank-transfer-details" 
                                     style="display: {{ $payment->payment_method == 'bank_transfer' ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account" class="form-label">Bank Account</label>
                                                <input type="text" name="bank_account" id="bank_account" 
                                                       class="form-control" 
                                                       value="{{ old('bank_account', $payment->bank_account ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="routing_number" class="form-label">Routing Number</label>
                                                <input type="text" name="routing_number" id="routing_number" 
                                                       class="form-control" 
                                                       value="{{ old('routing_number', $payment->routing_number ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Payment Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-calculator"></i> Payment Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Amount:</span>
                                    <strong id="payment-summary-amount">${{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Processing Fee:</span>
                                    <span id="payment-summary-fee">${{ number_format($payment->processing_fee ?? 0, 2) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Net Amount:</span>
                                    <strong id="payment-summary-net">
                                        ${{ number_format($payment->amount - ($payment->processing_fee ?? 0), 2) }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Balance -->
                        <div class="card mt-4" id="invoice-balance-card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Invoice Balance</h5>
                            </div>
                            <div class="card-body">
                                @if($payment->invoice)
                                    @php
                                        $balance = $payment->invoice->total - ($payment->invoice->paid_amount ?? 0);
                                        $remaining = $balance - $payment->amount;
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <strong id="invoice-total">${{ number_format($payment->invoice->total, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Amount Paid:</span>
                                        <span id="invoice-paid">
                                            ${{ number_format($payment->invoice->paid_amount ?? 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Current Payment:</span>
                                        <span id="current-payment">${{ number_format($payment->amount, 2) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Remaining Balance:</span>
                                        <strong id="remaining-balance" 
                                                class="{{ $remaining > 0 ? 'text-success' : ($remaining < 0 ? 'text-danger' : 'text-success') }}">
                                            {{ $remaining > 0 ? '$' . number_format($remaining, 2) : 
                                               ($remaining < 0 ? '$' . number_format(abs($remaining), 2) . ' overpaid' : '$0.00') }}
                                        </strong>
                                    </div>
                                @else
                                    <p class="text-muted">No invoice selected</p>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Payment
                                    </button>
                                    @if($payment->status == 'pending')
                                        <button type="submit" name="action" value="complete" class="btn btn-success">
                                            <i class="fas fa-check"></i> Update & Mark Complete
                                        </button>
                                    @endif
                                    @if($payment->status == 'completed')
                                        <button type="submit" name="action" value="pending" class="btn btn-warning">
                                            <i class="fas fa-clock"></i> Mark as Pending
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Payment History -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6><i class="fas fa-history"></i> Payment History</h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Created</h6>
                                            <p class="timeline-text">
                                                {{ $payment->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($payment->created_at != $payment->updated_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Last Updated</h6>
                                                <p class="timeline-text">
                                                    {{ $payment->updated_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($payment->status == 'completed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Completed</h6>
                                                <p class="timeline-text">
                                                    {{ $payment->updated_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 20px;
}

.timeline-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 2px;
}

.timeline-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}
</style>

@section('scripts')
<script>
$(document).ready(function() {
    let currentInvoiceBalance = 0;

    // Update amount helper when invoice is selected
    $('#invoice_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const balance = parseFloat(selectedOption.data('balance')) || 0;
        const amount = parseFloat($('#amount').val()) || 0;
        
        if (balance > 0) {
            $('#amount').attr('max', balance);
            $('#amount-help').text(`Maximum amount: $${balance.toFixed(2)}`);
            
            if (amount === 0 || amount > balance) {
                $('#amount').val(balance.toFixed(2));
            }
            
            $('#invoice-balance-card').show();
            $('#invoice-total').text('$' + balance.toFixed(2));
            $('#current-payment').text('$' + $('#amount').val());
            updateRemainingBalance();
        } else {
            $('#amount').removeAttr('max');
            $('#amount-help').text('Enter the payment amount');
            $('#invoice-balance-card').hide();
        }
        
        updateSummary();
    });

    // Update amount helper when amount changes
    $('#amount').on('input', function() {
        updateSummary();
        updateRemainingBalance();
    });

    // Update processing fee
    $('#processing_fee').on('input', function() {
        updateSummary();
    });

    // Show/hide additional details based on payment method
    $('#payment_method').change(function() {
        const method = $(this).val();
        
        // Hide all details
        $('#payment-method-details').hide();
        $('#credit-card-details, #check-details, #bank-transfer-details').hide();
        
        // Show relevant details
        if (method === 'credit_card' || method === 'stripe') {
            $('#payment-method-details').show();
            $('#credit-card-details').show();
        } else if (method === 'check') {
            $('#payment-method-details').show();
            $('#check-details').show();
        } else if (method === 'bank_transfer') {
            $('#payment-method-details').show();
            $('#bank-transfer-details').show();
        }
    });

    function updateSummary() {
        const amount = parseFloat($('#amount').val()) || 0;
        const processingFee = parseFloat($('#processing_fee').val()) || 0;
        const netAmount = amount - processingFee;
        
        $('#payment-summary-amount').text('$' + amount.toFixed(2));
        $('#payment-summary-fee').text('$' + processingFee.toFixed(2));
        $('#payment-summary-net').text('$' + netAmount.toFixed(2));
    }

    function updateRemainingBalance() {
        const selectedOption = $('#invoice_id').find('option:selected');
        const balance = parseFloat(selectedOption.data('balance')) || 0;
        const payment = parseFloat($('#amount').val()) || 0;
        const remaining = balance - payment;
        
        $('#current-payment').text('$' + payment.toFixed(2));
        
        if (remaining > 0) {
            $('#remaining-balance').text('$' + remaining.toFixed(2)).removeClass('text-danger').addClass('text-success');
        } else if (remaining < 0) {
            $('#remaining-balance').text('$' + Math.abs(remaining).toFixed(2) + ' overpaid').removeClass('text-success').addClass('text-danger');
        } else {
            $('#remaining-balance').text('$0.00').removeClass('text-danger').addClass('text-success');
        }
    }

    // Initialize
    updateSummary();
    
    // Trigger change events to set initial state
    $('#invoice_id').trigger('change');
});
</script>
@endsection
@endsection