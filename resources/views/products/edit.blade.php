@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Product: {{ $product->name }}</h1>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="product-form">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Product Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-box"></i> Product Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Product Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">SKU *</label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                            <div class="form-text">Stock Keeping Unit - unique identifier</div>
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Unit Price *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" name="price" value="{{ old('price', $product->price) }}" 
                                                       step="0.01" min="0" required>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cost_price" class="form-label">Cost Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                                       id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" 
                                                       step="0.01" min="0">
                                            </div>
                                            <div class="form-text">Cost to acquire/produce the item</div>
                                            @error('cost_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="profit_margin" class="form-label">Profit Margin</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="profit_margin" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-text">Automatically calculated</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="unit" class="form-label">Unit of Measurement</label>
                                            <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                                <option value="">Select Unit</option>
                                                <option value="piece" {{ old('unit', $product->unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                                                <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                                <option value="lb" {{ old('unit', $product->unit) == 'lb' ? 'selected' : '' }}>Pound</option>
                                                <option value="meter" {{ old('unit', $product->unit) == 'meter' ? 'selected' : '' }}>Meter</option>
                                                <option value="foot" {{ old('unit', $product->unit) == 'foot' ? 'selected' : '' }}>Foot</option>
                                                <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                                                <option value="gallon" {{ old('unit', $product->unit) == 'gallon' ? 'selected' : '' }}>Gallon</option>
                                                <option value="hour" {{ old('unit', $product->unit) == 'hour' ? 'selected' : '' }}>Hour</option>
                                                <option value="day" {{ old('unit', $product->unit) == 'day' ? 'selected' : '' }}>Day</option>
                                                <option value="service" {{ old('unit', $product->unit) == 'service' ? 'selected' : '' }}>Service</option>
                                            </select>
                                            @error('unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="barcode" class="form-label">Barcode</label>
                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                            <div class="form-text">UPC, EAN, or custom barcode</div>
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Management -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-warehouse"></i> Inventory Management</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="track_inventory" 
                                                       name="track_inventory" value="1" 
                                                       {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="track_inventory">
                                                    Track Inventory Levels
                                                </label>
                                            </div>
                                            <div class="form-text">Enable stock tracking for this product</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="low_stock_alert" class="form-label">Low Stock Alert Threshold</label>
                                            <input type="number" class="form-control @error('low_stock_alert') is-invalid @enderror" 
                                                   id="low_stock_alert" name="low_stock_alert" 
                                                   value="{{ old('low_stock_alert', $product->low_stock_alert) }}" min="0">
                                            <div class="form-text">Alert when stock falls below this level</div>
                                            @error('low_stock_alert')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Current Stock Quantity</label>
                                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                   id="stock_quantity" name="stock_quantity" 
                                                   value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" 
                                                   {{ !$product->track_inventory ? 'readonly' : '' }}>
                                            <div class="form-text">
                                                Current inventory level
                                                @if($product->track_inventory)
                                                    | Last updated: {{ $product->updated_at->format('M d, Y H:i') }}
                                                @else
                                                    | Stock tracking disabled
                                                @endif
                                            </div>
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Storage Location</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location', $product->location) }}">
                                            <div class="form-text">Warehouse location, shelf, bin, etc.</div>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reorder_point" class="form-label">Reorder Point</label>
                                            <input type="number" class="form-control @error('reorder_point') is-invalid @enderror" 
                                                   id="reorder_point" name="reorder_point" 
                                                   value="{{ old('reorder_point', $product->reorder_point) }}" min="0">
                                            <div class="form-text">Automatically reorder when stock reaches this level</div>
                                            @error('reorder_point')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_stock_level" class="form-label">Maximum Stock Level</label>
                                            <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror" 
                                                   id="max_stock_level" name="max_stock_level" 
                                                   value="{{ old('max_stock_level', $product->max_stock_level) }}" min="0">
                                            <div class="form-text">Maximum desired inventory level</div>
                                            @error('max_stock_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Status Alert -->
                                @if($product->track_inventory && $product->stock_quantity <= $product->low_stock_alert)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Low Stock Alert:</strong> Current stock ({{ $product->stock_quantity }}) is below the minimum threshold ({{ $product->low_stock_alert }}).
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Product Image -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-image"></i> Product Image</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Update Product Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <div class="form-text">Upload product image (JPG, PNG, GIF - Max 2MB)</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                @if($product->image)
                                    <div class="current-image mb-3">
                                        <label class="form-label">Current Image:</label>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="Current product image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px;">
                                        </div>
                                    </div>
                                @endif
                                
                                <div id="image-preview" class="text-center" style="display: none;">
                                    <label class="form-label">New Image Preview:</label>
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- Tax Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calculator"></i> Tax Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                               id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate) }}" 
                                               step="0.01" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="taxable" 
                                           name="taxable" value="1" 
                                           {{ old('taxable', $product->taxable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="taxable">
                                        Product is Taxable
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> Additional Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" 
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Product is Active
                                    </label>
                                    <div class="form-text">Inactive products won't appear in invoices</div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_featured" 
                                           name="is_featured" value="1" 
                                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Product
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_purchasing" 
                                           name="allow_purchasing" value="1" 
                                           {{ old('allow_purchasing', $product->allow_purchasing) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_purchasing">
                                        Allow Purchasing
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Product Statistics -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Product Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary">{{ $product->invoiceItems->sum('quantity') }}</h4>
                                            <small class="text-muted">Total Sold</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">${{ number_format($product->invoiceItems->sum('total'), 2) }}</h4>
                                        <small class="text-muted">Revenue Generated</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <small class="text-muted">
                                        Created: {{ $product->created_at->format('M d, Y') }}<br>
                                        Last Updated: {{ $product->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Update Product
                                </button>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-eye"></i> View Product
                                </a>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price and cost calculations
    const priceInput = document.getElementById('price');
    const costInput = document.getElementById('cost_price');
    const profitMarginInput = document.getElementById('profit_margin');
    
    function calculateProfitMargin() {
        const price = parseFloat(priceInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        
        if (price > 0 && cost > 0) {
            const margin = ((price - cost) / price * 100);
            profitMarginInput.value = margin.toFixed(2);
        } else {
            profitMarginInput.value = '0.00';
        }
    }
    
    priceInput.addEventListener('input', calculateProfitMargin);
    costInput.addEventListener('input', calculateProfitMargin);
    calculateProfitMargin(); // Initial calculation
    
    // Image preview
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Track inventory toggle
    const trackInventoryCheckbox = document.getElementById('track_inventory');
    const stockQuantityField = document.getElementById('stock_quantity');
    const inventoryFields = ['location', 'reorder_point', 'max_stock_level'];
    
    function toggleInventoryFields() {
        const disabled = !trackInventoryCheckbox.checked;
        
        // Stock quantity field behavior
        stockQuantityField.readOnly = disabled;
        
        // Other inventory fields
        inventoryFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            field.disabled = disabled;
            if (disabled) {
                field.value = '';
            }
        });
        
        // Update form text
        const formText = stockQuantityField.nextElementSibling;
        if (disabled) {
            formText.textContent = 'Stock tracking disabled';
        } else {
            formText.textContent = 'Current inventory level';
        }
    }
    
    trackInventoryCheckbox.addEventListener('change', toggleInventoryFields);
    toggleInventoryFields(); // Initial state
});
</script>
@endsection