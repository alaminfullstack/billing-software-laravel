@extends('layouts.app')

@section('title', 'Record Inventory Transaction')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus"></i> Record Inventory Transaction</h1>
                <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
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

            <form method="POST" action="{{ route('inventory-transactions.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Transaction Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="product_id" class="form-label">Product *</label>
                                            <select class="form-select @error('product_id') is-invalid @enderror" 
                                                    id="product_id" name="product_id" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}
                                                            data-current-stock="{{ $product->stock_quantity }}">
                                                        {{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Transaction Type *</label>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="purchase" {{ old('type') == 'purchase' ? 'selected' : '' }}>
                                                    Purchase (Stock Increase)
                                                </option>
                                                <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>
                                                    Sale (Stock Decrease)
                                                </option>
                                                <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>
                                                    Adjustment (Manual Change)
                                                </option>
                                                <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>
                                                    Return (Stock Increase)
                                                </option>
                                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>
                                                    Transfer (Manual Change)
                                                </option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity *</label>
                                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                                   id="quantity" name="quantity" 
                                                   value="{{ old('quantity') }}" 
                                                   min="1" step="1" required>
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted" id="quantity-help">
                                                Enter the quantity for this transaction
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="unit_cost" class="form-label">Unit Cost</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('unit_cost') is-invalid @enderror" 
                                                       id="unit_cost" name="unit_cost" 
                                                       value="{{ old('unit_cost') }}" 
                                                       min="0" step="0.01">
                                            </div>
                                            @error('unit_cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Cost per unit (for purchases)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Total Cost</label>
                                            <div class="form-control-plaintext">
                                                <strong id="total-cost">$0.00</strong>
                                            </div>
                                            <small class="form-text text-muted">
                                                Calculated automatically
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Optional notes about this transaction...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Current Stock Display -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Stock Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-12">
                                        <h4 class="text-muted" id="current-stock">0</h4>
                                        <small class="text-muted">Current Stock</small>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-info" id="quantity-display">0</h6>
                                        <small class="text-muted">Transaction</small>
                                    </div>
                                    <div class="col-4 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-arrow-right text-primary"></i>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-success" id="new-stock">0</h6>
                                        <small class="text-muted">New Stock</small>
                                    </div>
                                </div>

                                <div id="stock-warning" class="alert alert-warning mt-3" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This transaction would result in negative stock.
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Transaction Types:</strong>
                                    <ul class="mt-2 mb-0 small">
                                        <li><strong>Purchase:</strong> Increases stock</li>
                                        <li><strong>Sale:</strong> Decreases stock</li>
                                        <li><strong>Adjustment:</strong> Manual change</li>
                                        <li><strong>Return:</strong> Increases stock</li>
                                        <li><strong>Transfer:</strong> Manual change</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                        <i class="fas fa-save"></i> Record Transaction
                                    </button>
                                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
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
const productSelect = document.getElementById('product_id');
const typeSelect = document.getElementById('type');
const quantityInput = document.getElementById('quantity');
const unitCostInput = document.getElementById('unit_cost');
const totalCostDisplay = document.getElementById('total-cost');
const currentStockDisplay = document.getElementById('current-stock');
const quantityDisplay = document.getElementById('quantity-display');
const newStockDisplay = document.getElementById('new-stock');
const stockWarning = document.getElementById('stock-warning');
const submitBtn = document.getElementById('submit-btn');

function updateCalculations() {
    const selectedProduct = productSelect.options[productSelect.selectedIndex];
    const currentStock = selectedProduct ? parseInt(selectedProduct.dataset.currentStock) : 0;
    const type = typeSelect.value;
    const quantity = parseInt(quantityInput.value) || 0;
    const unitCost = parseFloat(unitCostInput.value) || 0;
    
    // Update displays
    currentStockDisplay.textContent = currentStock;
    quantityDisplay.textContent = quantity;
    
    // Calculate new stock based on transaction type
    let newStock = currentStock;
    if (type === 'purchase' || type === 'return') {
        newStock = currentStock + Math.abs(quantity);
    } else if (type === 'sale') {
        newStock = currentStock - Math.abs(quantity);
    } else if (type === 'adjustment' || type === 'transfer') {
        newStock = currentStock + quantity; // quantity can be negative for adjustments
    }
    
    newStockDisplay.textContent = newStock;
    
    // Calculate total cost
    const totalCost = unitCost * Math.abs(quantity);
    totalCostDisplay.textContent = '$' + totalCost.toFixed(2);
    
    // Show warning if stock would go negative
    if (newStock < 0) {
        stockWarning.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        stockWarning.style.display = 'none';
        submitBtn.disabled = !(selectedProduct && type && quantity > 0);
    }
}

productSelect.addEventListener('change', updateCalculations);
typeSelect.addEventListener('change', updateCalculations);
quantityInput.addEventListener('input', updateCalculations);
unitCostInput.addEventListener('input', updateCalculations);

// Initialize
updateCalculations();

// Prefill product from query string
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('product_id');
if (productId) {
    const option = Array.from(productSelect.options).find(opt => opt.value === productId);
    if (option) {
        option.selected = true;
        updateCalculations();
    }
}
</script>
@endsection
