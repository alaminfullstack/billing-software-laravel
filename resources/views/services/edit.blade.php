@extends('layouts.app')

@section('title', 'Edit Service')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Service: {{ $service->name }}</h1>
                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Services
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

            <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data" id="service-form">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Service Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-concierge-bell"></i> Service Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Service Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $service->name) }}" required>
                                            <div class="form-text">Enter a descriptive name for the service</div>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="service_code" class="form-label">Service Code</label>
                                            <input type="text" class="form-control @error('service_code') is-invalid @enderror" 
                                                   id="service_code" name="service_code" value="{{ old('service_code', $service->service_code) }}">
                                            <div class="form-text">Unique identifier for the service</div>
                                            @error('service_code')
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
                                                      id="description" name="description" rows="4" 
                                                      placeholder="Describe what this service includes...">{{ old('description', $service->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="base_rate" class="form-label">Base Rate *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('base_rate') is-invalid @enderror" 
                                                       id="base_rate" name="base_rate" value="{{ old('base_rate', $service->base_rate) }}" 
                                                       step="0.01" min="0" required>
                                            </div>
                                            <div class="form-text">Starting price for the service</div>
                                            @error('base_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="rate_type" class="form-label">Rate Type *</label>
                                            <select class="form-select @error('rate_type') is-invalid @enderror" 
                                                    id="rate_type" name="rate_type" required>
                                                <option value="">Select Rate Type</option>
                                                <option value="fixed" {{ old('rate_type', $service->rate_type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                                <option value="hourly" {{ old('rate_type', $service->rate_type) == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                                <option value="daily" {{ old('rate_type', $service->rate_type) == 'daily' ? 'selected' : '' }}>Daily Rate</option>
                                                <option value="project" {{ old('rate_type', $service->rate_type) == 'project' ? 'selected' : '' }}>Per Project</option>
                                                <option value="package" {{ old('rate_type', $service->rate_type) == 'package' ? 'selected' : '' }}>Package Deal</option>
                                            </select>
                                            @error('rate_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Estimated Duration</label>
                                            <input type="text" class="form-control @error('duration') is-invalid @enderror" 
                                                   id="duration" name="duration" value="{{ old('duration', $service->duration) }}" 
                                                   placeholder="e.g., 2 hours, 1 day">
                                            <div class="form-text">How long the service typically takes</div>
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                                       id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $service->tax_rate) }}" 
                                                       step="0.01" min="0" max="100">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            @error('tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Tiers -->
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-tags"></i> Pricing Tiers (Optional)</h6>
                                        <small class="text-muted">Add different rates for different quantities or customer types</small>
                                    </div>
                                    <div class="card-body">
                                        <div id="pricing-tiers">
                                            @if($service->pricing_tiers && is_array($service->pricing_tiers))
                                                @foreach($service->pricing_tiers as $index => $tier)
                                                    <div class="pricing-tier mb-3 border rounded p-3" id="tier-{{ $index }}">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tier Name</label>
                                                                <input type="text" class="form-control" name="tier_name[]" 
                                                                       value="{{ $tier['name'] ?? '' }}" placeholder="e.g., Basic, Premium">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Min Quantity</label>
                                                                <input type="number" class="form-control" name="tier_min[]" 
                                                                       value="{{ $tier['min'] ?? '' }}" min="1" placeholder="1">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Rate</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="number" class="form-control" name="tier_rate[]" 
                                                                           value="{{ $tier['rate'] ?? '' }}" step="0.01" min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Actions</label>
                                                                <button type="button" class="btn btn-outline-danger w-100" onclick="removeTier(this)">
                                                                    <i class="fas fa-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="pricing-tier mb-3 border rounded p-3">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="form-label">Tier Name</label>
                                                            <input type="text" class="form-control" name="tier_name[]" placeholder="e.g., Basic, Premium">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Min Quantity</label>
                                                            <input type="number" class="form-control" name="tier_min[]" min="1" placeholder="1">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Rate</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" class="form-control" name="tier_rate[]" step="0.01" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Actions</label>
                                                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeTier(this)">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTier()">
                                            <i class="fas fa-plus"></i> Add Another Tier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Service Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="delivery_time" class="form-label">Delivery Time</label>
                                            <input type="text" class="form-control @error('delivery_time') is-invalid @enderror" 
                                                   id="delivery_time" name="delivery_time" value="{{ old('delivery_time', $service->delivery_time) }}" 
                                                   placeholder="e.g., 24 hours, 1 week">
                                            <div class="form-text">How long until service is delivered</div>
                                            @error('delivery_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="requirements" class="form-label">Requirements</label>
                                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                                      id="requirements" name="requirements" rows="3" 
                                                      placeholder="What do you need from the client?">{{ old('requirements', $service->requirements) }}</textarea>
                                            @error('requirements')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="features" class="form-label">What's Included</label>
                                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                                      id="features" name="features" rows="4" 
                                                      placeholder="List what's included in this service...">{{ old('features', $service->features) }}</textarea>
                                            <div class="form-text">Use bullet points or separate lines for each feature</div>
                                            @error('features')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Service Image -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-image"></i> Service Image</h5>
                            </div>
                            <div class="card-body">
                                @if($service->image)
                                    <div class="mb-3">
                                        <label class="form-label">Current Image:</label>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $service->image) }}" 
                                                 alt="Current service image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px;">
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">{{ $service->image ? 'Update Service Image' : 'Service Image' }}</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <div class="form-text">Upload service image (JPG, PNG, GIF - Max 2MB)</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="image-preview" class="text-center" style="display: none;">
                                    <label class="form-label">New Image Preview:</label>
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- Service Performance -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Service Performance</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary">{{ $service->invoiceItems->count() }}</h4>
                                            <small class="text-muted">Times Sold</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">${{ number_format($service->invoiceItems->sum('total'), 2) }}</h4>
                                        <small class="text-muted">Revenue</small>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-info">{{ $service->averageRating() ?? 'N/A' }}</h5>
                                            <small class="text-muted">Avg. Rating</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-warning">{{ $service->totalHours() ?? 0 }}</h5>
                                        <small class="text-muted">Total Hours</small>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Created: {{ $service->created_at->format('M d, Y') }}<br>
                                        Last Updated: {{ $service->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> Service Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" 
                                           {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Service is Active
                                    </label>
                                    <div class="form-text">Inactive services won't appear in proposals</div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_featured" 
                                           name="is_featured" value="1" 
                                           {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Service
                                    </label>
                                    <div class="form-text">Highlight this service prominently</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_customization" 
                                           name="allow_customization" value="1" 
                                           {{ old('allow_customization', $service->allow_customization) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_customization">
                                        Allow Customization
                                    </label>
                                    <div class="form-text">Clients can modify this service</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="requires_deposit" 
                                           name="requires_deposit" value="1" 
                                           {{ old('requires_deposit', $service->requires_deposit) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_deposit">
                                        Requires Deposit
                                    </label>
                                    <div class="form-text">Need payment before starting work</div>
                                </div>

                                <div class="mb-3">
                                    <label for="deposit_percentage" class="form-label">Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('deposit_percentage') is-invalid @enderror" 
                                               id="deposit_percentage" name="deposit_percentage" 
                                               value="{{ old('deposit_percentage', $service->deposit_percentage) }}" min="0" max="100" step="1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Percentage required as deposit</div>
                                    @error('deposit_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Booking Settings -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calendar"></i> Booking Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="requires_appointment" 
                                           name="requires_appointment" value="1" 
                                           {{ old('requires_appointment', $service->requires_appointment) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_appointment">
                                        Requires Appointment
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_recurring" 
                                           name="is_recurring" value="1" 
                                           {{ old('is_recurring', $service->is_recurring) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">
                                        Recurring Service
                                    </label>
                                    <div class="form-text">Can be scheduled regularly</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="has_revisions" 
                                           name="has_revisions" value="1" 
                                           {{ old('has_revisions', $service->has_revisions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_revisions">
                                        Includes Revisions
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="max_revisions" class="form-label">Max Revisions</label>
                                    <input type="number" class="form-control @error('max_revisions') is-invalid @enderror" 
                                           id="max_revisions" name="max_revisions" 
                                           value="{{ old('max_revisions', $service->max_revisions) }}" min="0">
                                    <div class="form-text">Number of revision rounds included</div>
                                    @error('max_revisions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Update Service
                                </button>
                                <a href="{{ route('services.show', $service) }}" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-eye"></i> View Service
                                </a>
                                <a href="{{ route('services.index') }}" class="btn btn-secondary w-100">
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
let tierCounter = {{ $service->pricing_tiers && is_array($service->pricing_tiers) ? count($service->pricing_tiers) : 1 }};

function addTier() {
    tierCounter++;
    const tierHtml = `
        <div class="pricing-tier mb-3 border rounded p-3" id="tier-${tierCounter}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Tier Name</label>
                    <input type="text" class="form-control" name="tier_name[]" placeholder="e.g., Basic, Premium">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Quantity</label>
                    <input type="number" class="form-control" name="tier_min[]" min="1" placeholder="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rate</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="tier_rate[]" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actions</label>
                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeTier(this)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    document.getElementById('pricing-tiers').insertAdjacentHTML('beforeend', tierHtml);
}

function removeTier(button) {
    const tiers = document.querySelectorAll('.pricing-tier');
    if (tiers.length > 1) {
        button.closest('.pricing-tier').remove();
    } else {
        alert('You must have at least one pricing tier.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
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
    
    // Rate type change handler
    const rateTypeSelect = document.getElementById('rate_type');
    const durationField = document.getElementById('duration');
    
    function updatePlaceholder() {
        const type = rateTypeSelect.value;
        if (type === 'fixed' || type === 'project') {
            durationField.placeholder = 'e.g., 1 week';
        } else if (type === 'hourly' || type === 'daily') {
            durationField.placeholder = 'e.g., 2 hours, 1 day';
        } else {
            durationField.placeholder = 'e.g., 2 hours, 1 day';
        }
    }
    
    rateTypeSelect.addEventListener('change', updatePlaceholder);
    updatePlaceholder(); // Initial call
});
</script>
@endsection