@extends('layouts.app')

@section('title', 'Edit Tax Rate')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit"></i> Edit Tax Rate: {{ $tax->name }}</h1>
                <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tax Rates
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

            <form method="POST" action="{{ route('taxes.update', $tax) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Tax Rate Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Tax Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" 
                                                   value="{{ old('name', $tax->name) }}" 
                                                   placeholder="e.g., Sales Tax, VAT" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Tax Code *</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" 
                                                   value="{{ old('code', $tax->code) }}" 
                                                   placeholder="e.g., SALES, VAT" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Unique identifier for this tax rate</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rate" class="form-label">Tax Rate *</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('rate') is-invalid @enderror" 
                                                       id="rate" name="rate" 
                                                       value="{{ old('rate', $tax->rate) }}" 
                                                       min="0" step="0.01" required>
                                                <span class="input-group-text" id="rate-suffix">{{ $tax->type === 'percentage' ? '%' : '$' }}</span>
                                            </div>
                                            @error('rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Enter percentage or fixed amount</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Tax Type *</label>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="percentage" {{ old('type', $tax->type) == 'percentage' ? 'selected' : '' }}>
                                                    Percentage (%)
                                                </option>
                                                <option value="fixed" {{ old('type', $tax->type) == 'fixed' ? 'selected' : '' }}>
                                                    Fixed Amount ($)
                                                </option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Optional description...">{{ old('description', $tax->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $tax->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                        <small class="form-text text-muted">Only active tax rates can be used</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-primary" id="preview-rate">{{ $tax->formatted_rate }}</h2>
                                    <p class="text-muted mb-0" id="preview-type">{{ ucfirst($tax->type) }}</p>
                                </div>
                                
                                <hr>
                                
                                <h6>Usage Examples:</h6>
                                <div id="percentage-examples" class="mt-3" style="display: {{ $tax->type === 'percentage' ? 'block' : 'none' }};">
                                    <div class="example-calculation mb-2">
                                        <small class="text-muted">On $100.00:</small><br>
                                        <strong id="example-amount">${{ number_format(100 * $tax->rate / 100, 2) }}</strong>
                                    </div>
                                </div>
                                
                                <div id="fixed-examples" class="mt-3" style="display: {{ $tax->type === 'fixed' ? 'block' : 'none' }};">
                                    <div class="example-calculation mb-2">
                                        <small class="text-muted">Any amount:</small><br>
                                        <strong id="fixed-amount">${{ number_format($tax->rate, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Tax Rate
                            </button>
                            <a href="{{ route('taxes.show', $tax) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye"></i> View Tax Rate
                            </a>
                            <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const rateInput = document.getElementById('rate');
const typeSelect = document.getElementById('type');
const rateSuffix = document.getElementById('rate-suffix');
const previewRate = document.getElementById('preview-rate');
const previewType = document.getElementById('preview-type');
const percentageExamples = document.getElementById('percentage-examples');
const fixedExamples = document.getElementById('fixed-examples');
const exampleAmount = document.getElementById('example-amount');
const fixedAmount = document.getElementById('fixed-amount');

function updatePreview() {
    const rate = parseFloat(rateInput.value) || 0;
    const type = typeSelect.value;
    
    if (type === 'percentage') {
        rateSuffix.textContent = '%';
        previewRate.textContent = rate + '%';
        previewType.textContent = 'Percentage';
        percentageExamples.style.display = 'block';
        fixedExamples.style.display = 'none';
        exampleAmount.textContent = '$' + (100 * rate / 100).toFixed(2);
    } else if (type === 'fixed') {
        rateSuffix.textContent = '$';
        previewRate.textContent = '$' + rate.toFixed(2);
        previewType.textContent = 'Fixed Amount';
        percentageExamples.style.display = 'none';
        fixedExamples.style.display = 'block';
        fixedAmount.textContent = '$' + rate.toFixed(2);
    }
}

rateInput.addEventListener('input', updatePreview);
typeSelect.addEventListener('change', updatePreview);
</script>
@endsection
