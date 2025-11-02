@extends('layouts.app')

@section('title', 'Edit Quote')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit"></i> Edit Quote #{{ $quote->quote_number }}</h1>
                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Quote
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

            <form method="POST" action="{{ route('quotes.update', $quote) }}" id="quoteForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quote Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_id" class="form-label">Customer *</label>
                                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                                    id="customer_id" name="customer_id" required>
                                                <option value="">Select Customer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" 
                                                            {{ old('customer_id', $quote->customer_id) == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('customer_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="quote_date" class="form-label">Quote Date *</label>
                                            <input type="date" class="form-control @error('quote_date') is-invalid @enderror" 
                                                   id="quote_date" name="quote_date" 
                                                   value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required>
                                            @error('quote_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="valid_until" class="form-label">Valid Until *</label>
                                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                                   id="valid_until" name="valid_until" 
                                                   value="{{ old('valid_until', $quote->valid_until->format('Y-m-d')) }}" required>
                                            @error('valid_until')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Quote Items -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Quote Items</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItem">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                    
                                    <div id="quoteItems">
                                        @if(old('items'))
                                            @foreach(old('items') as $index => $item)
                                                <div class="quote-item border rounded p-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <label class="form-label">Description *</label>
                                                            <input type="text" class="form-control" 
                                                                   name="items[{{ $index }}][description]" 
                                                                   value="{{ $item['description'] ?? '' }}" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Quantity *</label>
                                                            <input type="number" class="form-control item-quantity" 
                                                                   name="items[{{ $index }}][quantity]" 
                                                                   value="{{ $item['quantity'] ?? '1' }}" 
                                                                   min="0.01" step="0.01" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Unit Price *</label>
                                                            <input type="number" class="form-control item-price" 
                                                                   name="items[{{ $index }}][unit_price]" 
                                                                   value="{{ $item['unit_price'] ?? '0' }}" 
                                                                   min="0" step="0.01" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Tax Rate (%)</label>
                                                            <input type="number" class="form-control item-tax" 
                                                                   name="items[{{ $index }}][tax_rate]" 
                                                                   value="{{ $item['tax_rate'] ?? '0' }}" 
                                                                   min="0" max="100" step="0.01">
                                                        </div>
                                                        <div class="col-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-item w-100">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="d-flex justify-content-between">
                                                                <small class="text-muted">Line Total:</small>
                                                                <strong>$0.00</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach($quote->items as $item)
                                                <div class="quote-item border rounded p-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <label class="form-label">Description *</label>
                                                            <input type="text" class="form-control" 
                                                                   name="items[{{ $loop->index }}][description]" 
                                                                   value="{{ $item->description }}" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Quantity *</label>
                                                            <input type="number" class="form-control item-quantity" 
                                                                   name="items[{{ $loop->index }}][quantity]" 
                                                                   value="{{ $item->quantity }}" 
                                                                   min="0.01" step="0.01" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Unit Price *</label>
                                                            <input type="number" class="form-control item-price" 
                                                                   name="items[{{ $loop->index }}][unit_price]" 
                                                                   value="{{ $item->unit_price }}" 
                                                                   min="0" step="0.01" required>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="form-label">Tax Rate (%)</label>
                                                            <input type="number" class="form-control item-tax" 
                                                                   name="items[{{ $loop->index }}][tax_rate]" 
                                                                   value="{{ $item->tax_rate }}" 
                                                                   min="0" max="100" step="0.01">
                                                        </div>
                                                        <div class="col-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-item w-100">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="d-flex justify-content-between">
                                                                <small class="text-muted">Line Total:</small>
                                                                <strong>${{ number_format($item->total_amount, 2) }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    @error('items')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="terms" class="form-label">Terms & Conditions</label>
                                            <textarea class="form-control" id="terms" name="terms" rows="4" 
                                                      placeholder="Enter terms and conditions...">{{ old('terms', $quote->terms) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                                      placeholder="Additional notes...">{{ old('notes', $quote->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quote Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label">Discount Amount</label>
                                    <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                           value="{{ old('discount_amount', $quote->discount_amount) }}" min="0" step="0.01">
                                </div>

                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <strong id="subtotal">${{ number_format($quote->subtotal, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax:</span>
                                        <strong id="tax">${{ number_format($quote->tax_amount, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <strong id="discount">${{ number_format($quote->discount_amount, 2) }}</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong id="total">${{ number_format($quote->total_amount, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Quote
                                    </button>
                                    <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemIndex = {{ old('items') ? count(old('items')) : $quote->items->count() }};

document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('quoteItems');
    const newItem = document.createElement('div');
    newItem.className = 'quote-item border rounded p-3 mb-3';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-12 mb-2">
                <label class="form-label">Description *</label>
                <input type="text" class="form-control" name="items[${itemIndex}][description]" required>
            </div>
            <div class="col-3">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" value="1" min="0.01" step="0.01" required>
            </div>
            <div class="col-3">
                <label class="form-label">Unit Price *</label>
                <input type="number" class="form-control item-price" name="items[${itemIndex}][unit_price]" value="0" min="0" step="0.01" required>
            </div>
            <div class="col-3">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" class="form-control item-tax" name="items[${itemIndex}][tax_rate]" value="0" min="0" max="100" step="0.01">
            </div>
            <div class="col-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item w-100">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="col-12 mt-2">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Line Total:</small>
                    <strong>$0.00</strong>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
    updateCalculations();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.quote-item').remove();
        if (document.querySelectorAll('.quote-item').length === 0) {
            document.getElementById('addItem').click();
        }
        updateCalculations();
    }
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-quantity') || 
        e.target.classList.contains('item-price') || 
        e.target.classList.contains('item-tax') ||
        e.target.id === 'discount_amount') {
        updateCalculations();
    }
});

function updateCalculations() {
    let subtotal = 0;
    let tax = 0;
    
    document.querySelectorAll('.quote-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(item.querySelector('.item-tax').value) || 0;
        
        const lineTotal = quantity * price;
        const lineTax = lineTotal * (taxRate / 100);
        
        item.querySelector('.d-flex justify-content-between strong').textContent = 
            '$' + (lineTotal + lineTax).toFixed(2);
        
        subtotal += lineTotal;
        tax += lineTax;
    });
    
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('discount').textContent = '$' + discount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

// Initialize calculations
updateCalculations();
</script>
@endsection
