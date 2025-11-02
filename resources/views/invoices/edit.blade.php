@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit"></i> Edit Invoice #{{ $invoice->invoice_number }}</h1>
                <div>
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> View Invoice
                    </a>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Invoices
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

            <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoiceForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Invoice Items -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-list"></i> Invoice Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="invoice-items">
                                    @if(isset($invoice->items) && count($invoice->items) > 0)
                                        @foreach($invoice->items as $index => $item)
                                            <div class="invoice-item mb-3 border rounded p-3">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label for="items[{{ $index }}][description]" class="form-label">Description *</label>
                                                        <input type="text" name="items[{{ $index }}][description]" 
                                                               id="items[{{ $index }}][description]" 
                                                               class="form-control" 
                                                               value="{{ $item->description }}" required>
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="items[{{ $index }}][quantity]" class="form-label">Quantity *</label>
                                                        <input type="number" name="items[{{ $index }}][quantity]" 
                                                               id="items[{{ $index }}][quantity]" 
                                                               class="form-control item-quantity" 
                                                               value="{{ $item->quantity }}" min="1" step="0.01" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="items[{{ $index }}][unit_price]" class="form-label">Unit Price *</label>
                                                        <input type="number" name="items[{{ $index }}][unit_price]" 
                                                               id="items[{{ $index }}][unit_price]" 
                                                               class="form-control item-price" 
                                                               value="{{ $item->unit_price }}" min="0" step="0.01" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="items[{{ $index }}][tax_rate]" class="form-label">Tax Rate (%)</label>
                                                        <input type="number" name="items[{{ $index }}][tax_rate]" 
                                                               id="items[{{ $index }}][tax_rate]" 
                                                               class="form-control item-tax" 
                                                               value="{{ $item->tax_rate ?? 0 }}" min="0" step="0.01">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger w-100 remove-item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="invoice-item mb-3 border rounded p-3">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="items[0][description]" class="form-label">Description *</label>
                                                    <input type="text" name="items[0][description]" 
                                                           id="items[0][description]" 
                                                           class="form-control" placeholder="Item description" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="items[0][quantity]" class="form-label">Quantity *</label>
                                                    <input type="number" name="items[0][quantity]" 
                                                           id="items[0][quantity]" 
                                                           class="form-control item-quantity" value="1" min="1" step="0.01" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="items[0][unit_price]" class="form-label">Unit Price *</label>
                                                    <input type="number" name="items[0][unit_price]" 
                                                           id="items[0][unit_price]" 
                                                           class="form-control item-price" value="0.00" min="0" step="0.01" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="items[0][tax_rate]" class="form-label">Tax Rate (%)</label>
                                                    <input type="number" name="items[0][tax_rate]" 
                                                           id="items[0][tax_rate]" 
                                                           class="form-control item-tax" value="0" min="0" step="0.01">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger w-100 remove-item" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <button type="button" id="add-item" class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Internal Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" 
                                              placeholder="Add any internal notes about this invoice...">{{ $invoice->notes ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_terms" class="form-label">Payment Terms</label>
                                    <textarea name="payment_terms" id="payment_terms" class="form-control" rows="2" 
                                              placeholder="e.g., Payment due within 30 days...">{{ $invoice->payment_terms ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Invoice Settings -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Invoice Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select name="customer_id" id="customer_id" class="form-select" required>
                                        <option value="">Select a customer</option>
                                        @foreach($customers ?? [] as $customer)
                                            <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="invoice_date" class="form-label">Invoice Date *</label>
                                            <input type="date" name="invoice_date" id="invoice_date" 
                                                   class="form-control" 
                                                   value="{{ $invoice->invoice_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Due Date *</label>
                                            <input type="date" name="due_date" id="due_date" 
                                                   class="form-control" 
                                                   value="{{ $invoice->due_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="sent" {{ $invoice->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                        <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        @if($invoice->due_date->isPast() && $invoice->status != 'paid')
                                            <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="discount_type" class="form-label">Discount Type</label>
                                    <select name="discount_type" id="discount_type" class="form-select">
                                        <option value="none" {{ ($invoice->discount_type ?? 'none') == 'none' ? 'selected' : '' }}>No Discount</option>
                                        <option value="percentage" {{ ($invoice->discount_type ?? 'none') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ ($invoice->discount_type ?? 'none') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="discount-field" style="display: {{ ($invoice->discount_type ?? 'none') != 'none' ? 'block' : 'none' }};">
                                    <label for="discount_value" class="form-label">Discount Value</label>
                                    <input type="number" name="discount_value" id="discount_value" 
                                           class="form-control" 
                                           value="{{ $invoice->discount_value ?? 0 }}" min="0" step="0.01">
                                    <small class="form-text text-muted" id="discount-help">
                                        @if(($invoice->discount_type ?? 'none') == 'percentage')
                                            Enter discount percentage (0-100)
                                        @else
                                            Enter discount amount
                                        @endif
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" 
                                           class="form-control" value="{{ $invoice->tax_rate ?? 0 }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-calculator"></i> Invoice Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">${{ number_format($invoice->subtotal, 2) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span id="tax-amount">${{ number_format($invoice->tax_amount, 2) }}</span>
                                </div>
                                
                                @if($invoice->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success" id="discount-row">
                                        <span>Discount:</span>
                                        <span id="discount-amount">-${{ number_format($invoice->discount_amount, 2) }}</span>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between mb-2 text-success" id="discount-row" style="display: none;">
                                        <span>Discount:</span>
                                        <span id="discount-amount">-$0.00</span>
                                    </div>
                                @endif
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between">
                                    <h6>Total:</h6>
                                    <h6 class="fw-bold text-primary" id="total">${{ number_format($invoice->total, 2) }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Invoice
                                    </button>
                                    @if($invoice->status == 'draft')
                                        <button type="submit" name="action" value="approve" class="btn btn-success">
                                            <i class="fas fa-check"></i> Update & Approve
                                        </button>
                                    @endif
                                    @if($invoice->status == 'sent')
                                        <button type="submit" name="action" value="send" class="btn btn-info">
                                            <i class="fas fa-paper-plane"></i> Update & Resend
                                        </button>
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

<!-- Invoice Item Template -->
<template id="invoice-item-template">
    <div class="invoice-item mb-3 border rounded p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Description *</label>
                <input type="text" class="form-control item-description" placeholder="Item description" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control item-quantity" value="1" min="1" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price *</label>
                <input type="number" class="form-control item-price" value="0.00" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" class="form-control item-tax" value="0" min="0" step="0.01">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger w-100 remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@section('scripts')
<script>
$(document).ready(function() {
    let itemCount = $('.invoice-item').length;

    // Add new invoice item
    $('#add-item').click(function() {
        const template = $('#invoice-item-template').html();
        const newItem = $(template);
        
        // Update name attributes
        newItem.find('input[name="items[0][description]"]').attr('name', `items[${itemCount}][description]`);
        newItem.find('input[name="items[0][quantity]"]').attr('name', `items[${itemCount}][quantity]`);
        newItem.find('input[name="items[0][unit_price]"]').attr('name', `items[${itemCount}][unit_price]`);
        newItem.find('input[name="items[0][tax_rate]"]').attr('name', `items[${itemCount}][tax_rate]`);
        
        $('#invoice-items').append(newItem);
        itemCount++;
        calculateTotals();
    });

    // Remove invoice item
    $(document).on('click', '.remove-item', function() {
        const items = $('.invoice-item');
        if (items.length > 1) {
            $(this).closest('.invoice-item').remove();
            calculateTotals();
        }
    });

    // Calculate totals when inputs change
    $(document).on('input', '.item-quantity, .item-price, .item-tax, #discount_value, #tax_rate', function() {
        calculateTotals();
    });

    // Discount type change
    $('#discount_type').change(function() {
        const discountType = $(this).val();
        if (discountType === 'none') {
            $('#discount-field').hide();
            $('#discount-row').hide();
        } else {
            $('#discount-field').show();
            $('#discount-row').show();
            updateDiscountHelp();
        }
        calculateTotals();
    });

    // Update discount help text
    function updateDiscountHelp() {
        const discountType = $('#discount_type').val();
        if (discountType === 'percentage') {
            $('#discount-help').text('Enter discount percentage (0-100)');
        } else {
            $('#discount-help').text('Enter discount amount');
        }
    }

    // Calculate invoice totals
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        $('.invoice-item').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const taxRate = parseFloat($(this).find('.item-tax').val()) || 0;

            const lineTotal = quantity * price;
            const lineTax = lineTotal * (taxRate / 100);

            subtotal += lineTotal;
            totalTax += lineTax;
        });

        // Apply discount
        let discount = 0;
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;

        if (discountType === 'percentage') {
            discount = subtotal * (discountValue / 100);
        } else if (discountType === 'fixed') {
            discount = discountValue;
        }

        const total = subtotal + totalTax - discount;

        // Update display
        $('#subtotal').text('$' + subtotal.toFixed(2));
        $('#tax-amount').text('$' + totalTax.toFixed(2));
        
        if (discount > 0) {
            $('#discount-amount').text('-$' + discount.toFixed(2));
            $('#discount-row').show();
        } else {
            $('#discount-row').hide();
        }
        
        $('#total').text('$' + total.toFixed(2));
    }

    // Initialize
    calculateTotals();
});
</script>
@endsection
@endsection