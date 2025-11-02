@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid">
    @if(isset($customer))
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-edit"></i> Edit Customer: {{ $customer->name }}</h1>
                    <div>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info me-2">
                            <i class="fas fa-eye"></i> View Customer
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Customers
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

                <form method="POST" action="{{ route('customers.update', $customer) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user"></i> Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" 
                                                       class="form-control @error('name') is-invalid @enderror" 
                                                       value="{{ old('name', $customer->name) }}" 
                                                       placeholder="Enter customer's full name" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_name" class="form-label">Company Name</label>
                                                <input type="text" name="company_name" id="company_name" 
                                                       class="form-control @error('company_name') is-invalid @enderror" 
                                                       value="{{ old('company_name', $customer->company_name) }}" 
                                                       placeholder="Company or organization name">
                                                @error('company_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email" 
                                                       class="form-control @error('email') is-invalid @enderror" 
                                                       value="{{ old('email', $customer->email) }}" 
                                                       placeholder="customer@example.com" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" name="phone" id="phone" 
                                                       class="form-control @error('phone') is-invalid @enderror" 
                                                       value="{{ old('phone', $customer->phone) }}" 
                                                       placeholder="+1 (555) 123-4567">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="mobile" class="form-label">Mobile Number</label>
                                                <input type="tel" name="mobile" id="mobile" 
                                                       class="form-control @error('mobile') is-invalid @enderror" 
                                                       value="{{ old('mobile', $customer->mobile) }}" 
                                                       placeholder="+1 (555) 987-6543">
                                                @error('mobile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="website" class="form-label">Website</label>
                                                <input type="url" name="website" id="website" 
                                                       class="form-control @error('website') is-invalid @enderror" 
                                                       value="{{ old('website', $customer->website) }}" 
                                                       placeholder="https://example.com">
                                                @error('website')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-map-marker-alt"></i> Address Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Street Address</label>
                                        <input type="text" name="address" id="address" 
                                               class="form-control @error('address') is-invalid @enderror" 
                                               value="{{ old('address', $customer->address) }}" 
                                               placeholder="Street address, P.O. box, company name, c/o">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" name="city" id="city" 
                                                       class="form-control @error('city') is-invalid @enderror" 
                                                       value="{{ old('city', $customer->city) }}" 
                                                       placeholder="City">
                                                @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="state" class="form-label">State/Province</label>
                                                <input type="text" name="state" id="state" 
                                                       class="form-control @error('state') is-invalid @enderror" 
                                                       value="{{ old('state', $customer->state) }}" 
                                                       placeholder="State or Province">
                                                @error('state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                                                <input type="text" name="zip_code" id="zip_code" 
                                                       class="form-control @error('zip_code') is-invalid @enderror" 
                                                       value="{{ old('zip_code', $customer->zip_code) }}" 
                                                       placeholder="ZIP or Postal code">
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
                                                <select name="country" id="country" 
                                                        class="form-select @error('country') is-invalid @enderror">
                                                    <option value="">Select Country</option>
                                                    <option value="US" {{ old('country', $customer->country) == 'US' ? 'selected' : '' }}>United States</option>
                                                    <option value="CA" {{ old('country', $customer->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                                    <option value="UK" {{ old('country', $customer->country) == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                    <option value="AU" {{ old('country', $customer->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                                                    <option value="DE" {{ old('country', $customer->country) == 'DE' ? 'selected' : '' }}>Germany</option>
                                                    <option value="FR" {{ old('country', $customer->country) == 'FR' ? 'selected' : '' }}>France</option>
                                                    <option value="IT" {{ old('country', $customer->country) == 'IT' ? 'selected' : '' }}>Italy</option>
                                                    <option value="ES" {{ old('country', $customer->country) == 'ES' ? 'selected' : '' }}>Spain</option>
                                                    <option value="NL" {{ old('country', $customer->country) == 'NL' ? 'selected' : '' }}>Netherlands</option>
                                                    <option value="other" {{ old('country', $customer->country) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> Additional Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tax_number" class="form-label">Tax/VAT Number</label>
                                                <input type="text" name="tax_number" id="tax_number" 
                                                       class="form-control @error('tax_number') is-invalid @enderror" 
                                                       value="{{ old('tax_number', $customer->tax_number) }}" 
                                                       placeholder="Tax identification number">
                                                @error('tax_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="credit_limit" class="form-label">Credit Limit</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="credit_limit" id="credit_limit" 
                                                           class="form-control @error('credit_limit') is-invalid @enderror" 
                                                           value="{{ old('credit_limit', $customer->credit_limit) }}" 
                                                           min="0" step="0.01" placeholder="0.00">
                                                </div>
                                                @error('credit_limit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_terms" class="form-label">Payment Terms</label>
                                        <select name="payment_terms" id="payment_terms" 
                                                class="form-select @error('payment_terms') is-invalid @enderror">
                                            <option value="">Select Payment Terms</option>
                                            <option value="net_15" {{ old('payment_terms', $customer->payment_terms) == 'net_15' ? 'selected' : '' }}>Net 15</option>
                                            <option value="net_30" {{ old('payment_terms', $customer->payment_terms) == 'net_30' ? 'selected' : '' }}>Net 30</option>
                                            <option value="net_45" {{ old('payment_terms', $customer->payment_terms) == 'net_45' ? 'selected' : '' }}>Net 45</option>
                                            <option value="net_60" {{ old('payment_terms', $customer->payment_terms) == 'net_60' ? 'selected' : '' }}>Net 60</option>
                                            <option value="due_on_receipt" {{ old('payment_terms', $customer->payment_terms) == 'due_on_receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                            <option value="cod" {{ old('payment_terms', $customer->payment_terms) == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                                        </select>
                                        @error('payment_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                                  rows="4" 
                                                  placeholder="Any additional notes about this customer...">{{ old('notes', $customer->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Customer Settings -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-cog"></i> Customer Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="customer_type" class="form-label">Customer Type</label>
                                        <select name="customer_type" id="customer_type" class="form-select @error('customer_type') is-invalid @enderror">
                                            <option value="individual" {{ old('customer_type', $customer->customer_type) == 'individual' ? 'selected' : '' }}>Individual</option>
                                            <option value="business" {{ old('customer_type', $customer->customer_type) == 'business' ? 'selected' : '' }}>Business</option>
                                            <option value="government" {{ old('customer_type', $customer->customer_type) == 'government' ? 'selected' : '' }}>Government</option>
                                            <option value="non_profit" {{ old('customer_type', $customer->customer_type) == 'non_profit' ? 'selected' : '' }}>Non-Profit</option>
                                        </select>
                                        @error('customer_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_tax_exempt" 
                                               id="is_tax_exempt" value="1" 
                                               {{ old('is_tax_exempt', $customer->is_tax_exempt) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_tax_exempt">
                                            Tax Exempt
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-info"></i> Account Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Customer ID:</span>
                                        <span>#{{ $customer->id }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Created:</span>
                                        <span>{{ $customer->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Last Updated:</span>
                                        <span>{{ $customer->updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Overview -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Financial Overview</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $totalInvoices = $customer->invoices()->count();
                                        $totalAmount = $customer->invoices()->sum('total_amount');
                                        $amountPaid = $customer->payments()->sum('amount');
                                        $balanceDue = $totalAmount - $amountPaid;
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Invoices:</span>
                                        <strong>{{ $totalInvoices }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <strong>${{ number_format($totalAmount, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Amount Paid:</span>
                                        <span class="text-success">${{ number_format($amountPaid, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Balance Due:</span>
                                        <strong class="{{ $balanceDue > 0 ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($balanceDue, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Customer
                                        </button>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i> View Customer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4>Customer not found</h4>
            <p>The requested customer could not be found.</p>
            <a href="{{ route('customers.index') }}" class="btn btn-primary">Back to Customers</a>
        </div>
    @endif
</div>
@endsection