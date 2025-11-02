@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus"></i> Record New Payment</h1>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
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

            @if(request('invoice_id'))
                @php
                    $selectedInvoice = $invoices ?? collect();
                    $selectedInvoice = $selectedInvoice->where('id', request('invoice_id'))->first();
                @endphp
                
                @if($selectedInvoice)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Recording payment for invoice: {{ $selectedInvoice->invoice_number }}</h6>
                        <p>Customer: <strong>{{ $selectedInvoice->customer->name ?? 'N/A' }}</strong> | 
                           Amount Due: <strong>${{ number_format($selectedInvoice->total - ($selectedInvoice->paid_amount ?? 0), 2) }}</strong></p>
                    </div>
                @endif
            @endif

            <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                @csrf
                
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
                                            <label for="invoice_id" class="form-label">
                                                Invoice <span class="text-danger">*</span>
                                                @if(request('invoice_id'))
                                                    <small class="text-success">(Pre-selected)</small>
                                                @endif
                                            </label>
                                            <select name="invoice_id" id="invoice_id" class="form-select" 
                                                    {{ request('invoice_id') ? 'disabled' : '' }}>
                                                <option value="">Select an invoice</option>
                                                @foreach($invoices ?? [] as $invoice)
                                                    @php
                                                        $balance = $invoice->total - ($invoice->paid_amount ?? 0);
                                                    @endphp
                                                    <option value="{{ $invoice->id }}" 
                                                            data-balance="{{ $balance }}"
                                                            {{ (request('invoice_id') == $invoice->id || old('invoice_id') == $invoice->id) ? 'selected' : '' }}>
                                                        {{ $invoice->invoice_number }} - {{ $invoice->customer->name ?? 'N/A' }} 
                                                        (Balance: ${{ number_format($balance, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if(request('invoice_id'))
                                                <input type="hidden" name="invoice_id" value="{{ request('invoice_id') }}">
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_id" class="form-label">Customer</label>
                                            <select name="customer_id" id="customer_id" class="form-select">
                                                <option value="">Select a customer (optional)</option>
                                                @foreach($customers ?? [] as $customer)
                                                    <option value="{{ $customer->id }}" 
                                                            {{ (request('customer_id') == $customer->id || old('customer_id') == $customer->id) ? 'selected' : '' }}>
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
                                                   value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                            <input type="number" name="amount" id="amount" 
                                                   class="form-control" 
                                                   value="{{ old('amount') }}" 
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
                                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label">Reference Number</label>
                                            <input type="text" name="reference_number" id="reference_number" 
                                                   class="form-control" 
                                                   value="{{ old('reference_number') }}" 
                                                   placeholder="Check number, transaction ID, etc.">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" 
                                              placeholder="Add any notes about this payment...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details (for specific payment methods) -->
                        <div class="card mt-4" id="payment-method-details" style="display: none;">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Additional Details</h5>
                            </div>
                            <div class="card-body">
                                <div id="credit-card-details" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                                <input type="text" name="transaction_id" id="transaction_id" 
                                                       class="form-control" value="{{ old('transaction_id') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="processing_fee" class="form-label">Processing Fee</label>
                                                <input type="number" name="processing_fee" id="processing_fee" 
                                                       class="form-control" value="{{ old('processing_fee', 0) }}" 
                                                       min="0" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="check-details" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="check_number" class="form-label">Check Number</label>
                                                <input type="text" name="check_number" id="check_number" 
                                                       class="form-control" value="{{ old('check_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_name" class="form-label">Bank Name</label>
                                                <input type="text" name="bank_name" id="bank_name" 
                                                       class="form-control" value="{{ old('bank_name') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="bank-transfer-details" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account" class="form-label">Bank Account</label>
                                                <input type="text" name="bank_account" id="bank_account" 
                                                       class="form-control" value="{{ old('bank_account') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="routing_number" class="form-label">Routing Number</label>
                                                <input type="text" name="routing_number" id="routing_number" 
                                                       class="form-control" value="{{ old('routing_number') }}">
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
                                    <strong id="payment-summary-amount">$0.00</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Processing Fee:</span>
                                    <span id="payment-summary-fee">$0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span>Net Amount:</span>
                                    <strong id="payment-summary-net">$0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Balance -->
                        <div class="card mt-4" id="invoice-balance-card" style="display: none;">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Invoice Balance</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Amount:</span>
                                    <strong id="invoice-total">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Amount Paid:</span>
                                    <span id="invoice-paid">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Current Payment:</span>
                                    <span id="current-payment">$0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span>Remaining Balance:</span>
                                    <strong id="remaining-balance" class="text-success">$0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="status" value="completed" class="btn btn-success">
                                        <i class="fas fa-check"></i> Record Payment
                                    </button>
                                    <button type="submit" name="status" value="pending" class="btn btn-warning">
                                        <i class="fas fa-clock"></i> Save as Pending
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Tips -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6><i class="fas fa-lightbulb"></i> Quick Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Payments can be recorded as pending or completed</li>
                                    <li><i class="fas fa-check text-success"></i> Reference numbers help with tracking</li>
                                    <li><i class="fas fa-check text-success"></i> Processing fees can be recorded for card payments</li>
                                    <li><i class="fas fa-check text-success"></i> Invoice status will be updated automatically</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
    $('#payment_method').trigger('change');
});
</script>
@endsection
@endsection