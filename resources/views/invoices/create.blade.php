@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus"></i> Create New Invoice</h1>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
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

            <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Invoice Items -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-list"></i> Invoice Items</h5>
                            </div>
                            <div class="card-body">
                                <div id="invoice-items">
                                    <div class="invoice-item mb-3 border rounded p-3">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="invoice_items[0][description]" class="form-label">Description *</label>
                                                <input type="text" name="invoice_items[0][description]" id="invoice_items[0][description]" 
                                                       class="form-control" placeholder="Item description" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="invoice_items[0][quantity]" class="form-label">Quantity *</label>
                                                <input type="number" name="invoice_items[0][quantity]" id="invoice_items[0][quantity]" 
                                                       class="form-control item-quantity" value="1" min="1" step="0.01" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="invoice_items[0][unit_price]" class="form-label">Unit Price *</label>
                                                <input type="number" name="invoice_items[0][unit_price]" id="invoice_items[0][unit_price]" 
                                                       class="form-control item-price" value="0.00" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="invoice_items[0][tax_rate]" class="form-label">Tax Rate (%)</label>
                                                <input type="number" name="invoice_items[0][tax_rate]" id="invoice_items[0][tax_rate]" 
                                                       class="form-control item-tax" value="0" min="0" step="0.01">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger w-100 remove-item" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
                                              placeholder="Add any internal notes about this invoice..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_terms" class="form-label">Payment Terms</label>
                                    <textarea name="payment_terms" id="payment_terms" class="form-control" rows="2" 
                                              placeholder="e.g., Payment due within 30 days..."></textarea>
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
                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="issue_date" class="form-label">Invoice Date *</label>
                                            <input type="date" name="issue_date" id="issue_date" 
                                                   class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Due Date *</label>
                                            <input type="date" name="due_date" id="due_date" 
                                                   class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="draft">Draft</option>
                                        <option value="sent">Sent</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label">Discount Amount</label>
                                    <input type="number" name="discount_amount" id="discount_amount" 
                                           class="form-control" value="0" min="0" step="0.01">
                                </div>

                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" 
                                           class="form-control" value="0" min="0" step="0.01">
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
                                    <span id="subtotal">$0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span id="tax-amount">$0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2 text-success" id="discount-row" style="display: none;">
                                    <span>Discount:</span>
                                    <span id="discount-amount">-$0.00</span>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between">
                                    <h6>Total:</h6>
                                    <h6 class="fw-bold text-primary" id="total">$0.00</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="draft" class="btn btn-secondary">
                                        <i class="fas fa-save"></i> Save as Draft
                                    </button>
                                    <button type="submit" name="action" value="send" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Save & Send
                                    </button>
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
                <input type="text" class="form-control item-description" name="invoice_items[0][description]" placeholder="Item description" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control item-quantity" name="invoice_items[0][quantity]" value="1" min="1" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price *</label>
                <input type="number" class="form-control item-price" name="invoice_items[0][unit_price]" value="0.00" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" class="form-control item-tax" name="invoice_items[0][tax_rate]" value="0" min="0" step="0.01">
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
    let itemCount = 1;

    // Add new invoice item
    $('#add-item').click(function() {
        const template = $('#invoice-item-template').html();
        const newItem = $(template);
        
        // Update name attributes
        newItem.find('input[name="invoice_items[0][description]"]').attr('name', `invoice_items[${itemCount}][description]`);
        newItem.find('input[name="invoice_items[0][quantity]"]').attr('name', `invoice_items[${itemCount}][quantity]`);
        newItem.find('input[name="invoice_items[0][unit_price]"]').attr('name', `invoice_items[${itemCount}][unit_price]`);
        newItem.find('input[name="invoice_items[0][tax_rate]"]').attr('name', `invoice_items[${itemCount}][tax_rate]`);
        
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
    $(document).on('input', '.item-quantity, .item-price, .item-tax, #discount_amount', function() {
        calculateTotals();
    });

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
        let discount = parseFloat($('#discount_amount').val()) || 0;
        
        const total = subtotal + totalTax - discount;

        // Update display
        $('#subtotal').text('$' + subtotal.toFixed(2));
        $('#tax-amount').text('$' + totalTax.toFixed(2));
        
        if (discount > 0) {
            $('#discount-amount').text('-$' + discount.toFixed(2));
            $('#discount-row').show();
        } else {
            $('#discount-amount').text('-$0.00');
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