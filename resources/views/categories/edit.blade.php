@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Category: {{ $category->name }}</h1>
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

            <form action="{{ route('categories.update', $category) }}" method="POST" id="category-form">
                @csrf
                @method('PUT')
                
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
                                                   id="name" name="name" value="{{ old('name', $category->name) }}" required>
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
                                                <option value="product" {{ old('type', $category->type) == 'product' ? 'selected' : '' }}>Product Category</option>
                                                <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense Category</option>
                                                <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Income Category</option>
                                                <option value="service" {{ old('type', $category->type) == 'service' ? 'selected' : '' }}>Service Category</option>
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
                                                      placeholder="Describe what this category is used for...">{{ old('description', $category->description) }}</textarea>
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
                                                       id="color" name="color" value="{{ old('color', $category->color) }}">
                                                <span class="input-group-text" id="color-preview">{{ $category->color }}</span>
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
                                                @foreach($parentCategories as $parentCategory)
                                                    @if($parentCategory->id !== $category->id && !$category->isDescendantOf($parentCategory))
                                                        <option value="{{ $parentCategory->id }}" 
                                                                {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                                            {{ str_repeat('— ', $parentCategory->depth ?? 0) }}{{ $parentCategory->name }}
                                                        </option>
                                                    @endif
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
                                                   id="icon" name="icon" value="{{ old('icon', $category->icon) }}" 
                                                   placeholder="fas fa-tag">
                                            <div class="form-text">Font Awesome icon class (optional)</div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($category->children->count() > 0)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Subcategories:</strong> This category has {{ $category->children->count() }} subcategory(ies). 
                                        Changing the parent will move all subcategories as well.
                                    </div>
                                @endif
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
                                                       value="{{ old('default_tax_rate', $category->default_tax_rate) }}" step="0.01" min="0" max="100">
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
                                                   id="account_code" name="account_code" value="{{ old('account_code', $category->account_code) }}">
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
                                                   name="is_taxable" value="1" 
                                                   {{ old('is_taxable', $category->is_taxable) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_taxable">
                                                Taxable Category
                                            </label>
                                            <div class="form-text">Items in this category are subject to tax</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" value="1" 
                                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
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
                                    <div class="border rounded p-4 mb-3" style="border-color: {{ $category->color }} !important;">
                                        <div id="preview-icon" style="font-size: 2rem; margin-bottom: 1rem; color: {{ $category->color }};">
                                            <i class="{{ $category->icon ?: 'fas fa-folder' }}"></i>
                                        </div>
                                        <h6 id="preview-name" class="mb-1">{{ $category->name }}</h6>
                                        <p id="preview-type" class="text-muted mb-2">{{ ucfirst($category->type) }} Category</p>
                                        <div id="preview-description" class="text-muted small">{{ $category->description ?: 'No description' }}</div>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($category->parent)
                                            <span class="badge bg-secondary">Subcategory of: {{ $category->parent->name }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">No Parent</span>
                                        @endif
                                        @if($category->children->count() > 0)
                                            <span class="badge bg-info">{{ $category->children->count() }} Subcategories</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Statistics -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Usage Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary">{{ $category->products_count ?? 0 }}</h4>
                                            <small class="text-muted">Products</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $category->expenses_count ?? 0 }}</h4>
                                        <small class="text-muted">Expenses</small>
                                    </div>
                                </div>
                                
                                @if($category->children->count() > 0)
                                    <div class="alert alert-info">
                                        <i class="fas fa-sitemap"></i>
                                        <strong>Subcategories:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($category->children as $child)
                                                <li>{{ $child->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
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
                                           name="is_default" value="1" 
                                           {{ old('is_default', $category->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Category
                                    </label>
                                    <div class="form-text">Use as default for new items of this type</div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_in_menu" 
                                           name="show_in_menu" value="1" 
                                           {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_menu">
                                        Show in Navigation Menu
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_subcategories" 
                                           name="allow_subcategories" value="1" 
                                           {{ old('allow_subcategories', $category->allow_subcategories) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_subcategories">
                                        Allow Subcategories
                                    </label>
                                    <div class="form-text">Enable creating subcategories under this one</div>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info"></i> System Information</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-6">Created:</dt>
                                    <dd class="col-6">{{ $category->created_at->format('M d, Y') }}</dd>
                                    
                                    <dt class="col-6">Updated:</dt>
                                    <dd class="col-6">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                                    
                                    <dt class="col-6">Depth:</dt>
                                    <dd class="col-6">{{ $category->depth ?? 0 }}</dd>
                                    
                                    <dt class="col-6">ID:</dt>
                                    <dd class="col-6">#{{ $category->id }}</dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Update Category
                                </button>
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-eye"></i> View Category
                                </a>
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
        previewDescription.textContent = descriptionInput.value || 'No description';
        
        // Update color
        const color = colorInput.value || '#007bff';
        previewColor.style.borderColor = color;
        previewIcon.style.color = color;
        colorPreview.textContent = color;
        
        // Update icon
        const iconClass = iconInput.value || 'fas fa-folder';
        previewIcon.innerHTML = `<i class="${iconClass}"></i>`;
        
        // Update parent info
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const parentBadge = document.querySelector('.d-flex.justify-content-center.gap-2');
        if (selectedOption && selectedOption.value) {
            let subcategoryCount = '{{ $category->children->count() }}';
            parentBadge.innerHTML = `
                <span class="badge bg-secondary">Subcategory of: ${selectedOption.text}</span>
                ${subcategoryCount > 0 ? `<span class="badge bg-info">${subcategoryCount} Subcategories</span>` : ''}
            `;
        } else {
            let subcategoryCount = '{{ $category->children->count() }}';
            parentBadge.innerHTML = `
                <span class="badge bg-light text-dark">No Parent</span>
                ${subcategoryCount > 0 ? `<span class="badge bg-info">${subcategoryCount} Subcategories</span>` : ''}
            `;
        }
    }
    
    function filterParentCategories() {
        const selectedType = typeSelect.value;
        const options = parentSelect.querySelectorAll('option');
        const categoryId = {{ $category->id }};
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block'; // Always show "No Parent"
            } else if (option.value == categoryId) {
                option.style.display = 'none'; // Hide self
            } else {
                // In a real application, you would check the parent category's type
                option.style.display = 'block';
            }
        });
    }
    
    // Event listeners
    nameInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', () => {
        updatePreview();
        filterParentCategories();
    });
    descriptionInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    iconInput.addEventListener('input', updatePreview);
    parentSelect.addEventListener('change', updatePreview);
    
    // Initialize preview
    updatePreview();
    filterParentCategories();
});
</script>
@endsection