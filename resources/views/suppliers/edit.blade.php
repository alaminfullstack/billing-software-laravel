@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit"></i> Edit Supplier: {{ $supplier->name }}</h1>
                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Supplier
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

            <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Supplier Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Company Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" 
                                                   value="{{ old('name', $supplier->name) }}" 
                                                   placeholder="e.g., ABC Corporation" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status *</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                                <option value="blocked" {{ old('status', $supplier->status) == 'blocked' ? 'selected' : '' }}>
                                                    Blocked
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" 
                                                   value="{{ old('email', $supplier->email) }}" 
                                                   placeholder="contact@company.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" 
                                                   value="{{ old('phone', $supplier->phone) }}" 
                                                   placeholder="+1-555-0123">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" 
                                           value="{{ old('address', $supplier->address) }}" 
                                           placeholder="Street address, building, etc.">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" 
                                                   value="{{ old('city', $supplier->city) }}" 
                                                   placeholder="City name">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" 
                                                   value="{{ old('state', $supplier->state) }}" 
                                                   placeholder="State or province">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                                   id="zip_code" name="zip_code" 
                                                   value="{{ old('zip_code', $supplier->zip_code) }}" 
                                                   placeholder="ZIP or postal code">
                                            @error('zip_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" 
                                                   value="{{ old('country', $supplier->country) }}" 
                                                   placeholder="Country name">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                                   id="website" name="website" 
                                                   value="{{ old('website', $supplier->website) }}" 
                                                   placeholder="https://www.example.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">Tax/VAT Number</label>
                                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                                   id="tax_number" name="tax_number" 
                                                   value="{{ old('tax_number', $supplier->tax_number) }}" 
                                                   placeholder="Tax identification number">
                                            @error('tax_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Required for business suppliers</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes about this supplier...">{{ old('notes', $supplier->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Business Supplier:</strong>
                                    @if($supplier->tax_number)
                                        This supplier has a tax number and is considered a business.
                                    @else
                                        This supplier doesn't have a tax number on file.
                                    @endif
                                </div>

                                @if($supplier->expenses->count() > 0)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Note:</strong> This supplier has {{ $supplier->expenses->count() }} associated expenses.
                                    </div>
                                @endif

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Supplier
                                    </button>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i> View Supplier
                                    </a>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
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
@endsection
