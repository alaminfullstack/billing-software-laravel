@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Add New Category</h1>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
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

            <form action="{{ route('categories.store') }}" method="POST" id="category-form">
                @csrf
                
                <div class="row">
                    <!-- Main Category Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-folder"></i> Category Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Category Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            <div class="form-text">Enter a descriptive name for the category</div>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Category Type *</label>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Product Category</option>
                                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense Category</option>
                                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income Category</option>
                                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service Category</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Describe what this category is used for...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="color" class="form-label">Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                                       id="color" name="color" value="{{ old('color', '#007bff') }}">
                                                <span class="input-group-text" id="color-preview">#007bff</span>
                                            </div>
                                            <div class="form-text">Choose a color for category identification</div>
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="parent_id" class="form-label">Parent Category</label>
                                            <select class="form-select @error('parent_id') is-invalid @enderror" 
                                                    id="parent_id" name="parent_id">
                                                <option value="">No Parent (Top Level)</option>
                                                @foreach($parentCategories as $category)
                                                    <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                                        {{ str_repeat('— ', $category->depth ?? 0) }}{{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Create sub-categories by selecting a parent</div>
                                            @error('parent_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="icon" class="form-label">Icon</label>
                                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                   id="icon" name="icon" value="{{ old('icon') }}" 
                                                   placeholder="fas fa-tag">
                                            <div class="form-text">Font Awesome icon class (optional)</div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tax and Accounting Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calculator"></i> Tax & Accounting Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default_tax_rate" class="form-label">Default Tax Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('default_tax_rate') is-invalid @enderror" 
                                                       id="default_tax_rate" name="default_tax_rate" 
                                                       value="{{ old('default_tax_rate', 0) }}" step="0.01" min="0" max="100">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-text">Applied to items in this category by default</div>
                                            @error('default_tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="account_code" class="form-label">Account Code</label>
                                            <input type="text" class="form-control @error('account_code') is-invalid @enderror" 
                                                   id="account_code" name="account_code" value="{{ old('account_code') }}">
                                            <div class="form-text">Accounting system account reference</div>
                                            @error('account_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_taxable" 
                                                   name="is_taxable" value="1" {{ old('is_taxable', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_taxable">
                                                Taxable Category
                                            </label>
                                            <div class="form-text">Items in this category are subject to tax</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active Category
                                            </label>
                                            <div class="form-text">Inactive categories won't be available for selection</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Category Preview -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-eye"></i> Category Preview</h5>
                            </div>
                            <div class="card-body">
                                <div id="category-preview" class="text-center">
                                    <div class="border rounded p-4 mb-3">
                                        <div id="preview-icon" style="font-size: 2rem; margin-bottom: 1rem; color: #007bff;">
                                            <i class="fas fa-folder"></i>
                                        </div>
                                        <h6 id="preview-name" class="mb-1">Category Name</h6>
                                        <p id="preview-type" class="text-muted mb-2">Category Type</p>
                                        <div id="preview-description" class="text-muted small">Category description will appear here</div>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <span class="badge bg-light text-dark">No Parent</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> Additional Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_default" 
                                           name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Category
                                    </label>
                                    <div class="form-text">Use as default for new items of this type</div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_in_menu" 
                                           name="show_in_menu" value="1" {{ old('show_in_menu', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_menu">
                                        Show in Navigation Menu
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_subcategories" 
                                           name="allow_subcategories" value="1" {{ old('allow_subcategories', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_subcategories">
                                        Allow Subcategories
                                    </label>
                                    <div class="form-text">Enable creating subcategories under this one</div>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Statistics -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Usage Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Category Usage</strong>
                                    <p class="mb-0 mt-2">
                                        This category will be available for:
                                    </p>
                                    <ul class="mb-0 mt-2">
                                        <li id="usage-products" style="display: none;">Product management</li>
                                        <li id="usage-expenses" style="display: none;">Expense tracking</li>
                                        <li id="usage-income" style="display: none;">Income recording</li>
                                        <li id="usage-services" style="display: none;">Service catalog</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Create Category
                                </button>
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary w-100">
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
    // Form elements
    const nameInput = document.getElementById('name');
    const typeSelect = document.getElementById('type');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const colorPreview = document.getElementById('color-preview');
    const iconInput = document.getElementById('icon');
    const parentSelect = document.getElementById('parent_id');
    
    // Preview elements
    const previewName = document.getElementById('preview-name');
    const previewType = document.getElementById('preview-type');
    const previewDescription = document.getElementById('preview-description');
    const previewIcon = document.getElementById('preview-icon');
    const previewColor = document.getElementById('category-preview');
    
    // Usage elements
    const usageProducts = document.getElementById('usage-products');
    const usageExpenses = document.getElementById('usage-expenses');
    const usageIncome = document.getElementById('usage-income');
    const usageServices = document.getElementById('usage-services');
    
    function updatePreview() {
        // Update name
        previewName.textContent = nameInput.value || 'Category Name';
        
        // Update type
        const typeText = {
            'product': 'Product Category',
            'expense': 'Expense Category',
            'income': 'Income Category',
            'service': 'Service Category'
        };
        previewType.textContent = typeText[typeSelect.value] || 'Category Type';
        
        // Update description
        previewDescription.textContent = descriptionInput.value || 'Category description will appear here';
        
        // Update color
        const color = colorInput.value || '#007bff';
        previewColor.style.borderColor = color;
        previewIcon.style.color = color;
        colorPreview.textContent = color;
        
        // Update icon
        const iconClass = iconInput.value || 'fas fa-folder';
        previewIcon.innerHTML = `<i class="${iconClass}"></i>`;
        
        // Update parent info
        const parentSelect = document.getElementById('parent_id');
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const parentBadge = document.querySelector('.d-flex.justify-content-center.gap-2');
        if (selectedOption && selectedOption.value) {
            parentBadge.innerHTML = `<span class="badge bg-secondary">Subcategory of: ${selectedOption.text}</span>`;
        } else {
            parentBadge.innerHTML = `<span class="badge bg-light text-dark">No Parent</span>`;
        }
    }
    
    function updateUsage() {
        // Hide all usage items
        usageProducts.style.display = 'none';
        usageExpenses.style.display = 'none';
        usageIncome.style.display = 'none';
        usageServices.style.display = 'none';
        
        // Show relevant usage based on type
        switch(typeSelect.value) {
            case 'product':
                usageProducts.style.display = 'block';
                break;
            case 'expense':
                usageExpenses.style.display = 'block';
                break;
            case 'income':
                usageIncome.style.display = 'block';
                break;
            case 'service':
                usageServices.style.display = 'block';
                break;
        }
    }
    
    function filterParentCategories() {
        const selectedType = typeSelect.value;
        const options = parentSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block'; // Always show "No Parent"
            } else {
                // In a real application, you would check the parent category's type
                // For now, we'll show all categories as parents
                option.style.display = 'block';
            }
        });
    }
    
    // Event listeners
    nameInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', () => {
        updatePreview();
        updateUsage();
        filterParentCategories();
    });
    descriptionInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    iconInput.addEventListener('input', updatePreview);
    parentSelect.addEventListener('change', updatePreview);
    
    // Initialize preview
    updatePreview();
    updateUsage();
    filterParentCategories();
});
</script>
@endsection