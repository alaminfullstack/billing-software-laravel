@extends('layouts.app')

@section('title', 'Add New Expense')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Add New Expense</h1>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Expenses
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

            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" id="expense-form">
                @csrf
                
                <div class="row">
                    <!-- Main Expense Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-receipt"></i> Expense Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Expense Title *</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            <div class="form-text">Brief description of the expense</div>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" value="{{ old('amount') }}" 
                                                       step="0.01" min="0" required>
                                            </div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expense_date" class="form-label">Expense Date *</label>
                                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                                   id="expense_date" name="expense_date" 
                                                   value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                            @error('expense_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category *</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" name="payment_method">
                                                <option value="">Select Method</option>
                                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="digital_wallet" {{ old('payment_method') == 'digital_wallet' ? 'selected' : '' }}>Digital Wallet</option>
                                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="vendor" class="form-label">Vendor/Supplier</label>
                                            <input type="text" class="form-control @error('vendor') is-invalid @enderror" 
                                                   id="vendor" name="vendor" value="{{ old('vendor') }}">
                                            <div class="form-text">Name of the person or company you paid</div>
                                            @error('vendor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label">Reference Number</label>
                                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                                   id="reference_number" name="reference_number" value="{{ old('reference_number') }}">
                                            <div class="form-text">Invoice #, receipt #, check #, etc.</div>
                                            @error('reference_number')
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
                                                      placeholder="Detailed description of the expense...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Receipt Attachment -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-paperclip"></i> Receipt Attachment</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="receipt" class="form-label">Upload Receipt</label>
                                    <input type="file" class="form-control @error('receipt') is-invalid @enderror" 
                                           id="receipt" name="receipt" accept="image/*,application/pdf">
                                    <div class="form-text">Upload receipt image or PDF (Max 5MB)</div>
                                    @error('receipt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="receipt-preview" class="text-center" style="display: none;">
                                    <div id="receipt-image-preview">
                                        <img id="receipt-preview-img" src="" alt="Receipt Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                                    </div>
                                    <div id="receipt-pdf-preview" class="d-none">
                                        <i class="fas fa-file-pdf fa-5x text-danger mb-2"></i>
                                        <p class="mb-0">PDF receipt uploaded</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Tax Information -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calculator"></i> Tax Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="tax_amount" class="form-label">Tax Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('tax_amount') is-invalid @enderror" 
                                               id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" 
                                               step="0.01" min="0">
                                    </div>
                                    <div class="form-text">Sales tax, VAT, or other taxes paid</div>
                                    @error('tax_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="tax_rate" value="0" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Auto-calculated from amount and tax</div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_tax_deductible" 
                                           name="is_tax_deductible" value="1" {{ old('is_tax_deductible') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_tax_deductible">
                                        Tax Deductible
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}">
                                    <div class="form-text">Where the expense was incurred</div>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="project" class="form-label">Related Project</label>
                                    <input type="text" class="form-control @error('project') is-invalid @enderror" 
                                           id="project" name="project" value="{{ old('project') }}">
                                    <div class="form-text">Associated project or job</div>
                                    @error('project')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_recurring" 
                                           name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">
                                        Recurring Expense
                                    </label>
                                    <div class="form-text">This expense occurs regularly</div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Status -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user-check"></i> Approval Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="requires_approval" 
                                           name="requires_approval" value="1" 
                                           {{ old('requires_approval', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_approval">
                                        Requires Approval
                                    </label>
                                </div>

                                @if(auth()->user()->can('approve-expenses'))
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <input type="hidden" name="status" value="pending">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Expense submitted for approval
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Create Expense
                                </button>
                                <a href="{{ route('expenses.index') }}" class="btn btn-secondary w-100">
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
    // Tax calculations
    const amountInput = document.getElementById('amount');
    const taxAmountInput = document.getElementById('tax_amount');
    const taxRateInput = document.getElementById('tax_rate');
    
    function calculateTaxRate() {
        const amount = parseFloat(amountInput.value) || 0;
        const taxAmount = parseFloat(taxAmountInput.value) || 0;
        
        if (amount > 0 && taxAmount >= 0) {
            const rate = (taxAmount / amount * 100);
            taxRateInput.value = rate.toFixed(2);
        } else {
            taxRateInput.value = '0.00';
        }
    }
    
    amountInput.addEventListener('input', calculateTaxRate);
    taxAmountInput.addEventListener('input', calculateTaxRate);
    
    // Receipt preview
    const receiptInput = document.getElementById('receipt');
    const receiptPreview = document.getElementById('receipt-preview');
    const receiptImagePreview = document.getElementById('receipt-image-preview');
    const receiptPdfPreview = document.getElementById('receipt-pdf-preview');
    const receiptPreviewImg = document.getElementById('receipt-preview-img');
    
    receiptInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileType = file.type;
            
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    receiptPreviewImg.src = e.target.result;
                    receiptImagePreview.classList.remove('d-none');
                    receiptPdfPreview.classList.add('d-none');
                    receiptPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                receiptImagePreview.classList.add('d-none');
                receiptPdfPreview.classList.remove('d-none');
                receiptPreview.style.display = 'block';
            }
        } else {
            receiptPreview.style.display = 'none';
        }
    });
    
    // Recurring expense toggle
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurringFields = ['recurring_frequency', 'next_due_date'];
    
    function toggleRecurringFields() {
        const disabled = !isRecurringCheckbox.checked;
        // Add recurring frequency and next due date fields here if needed
        // For now, just logging the toggle
        console.log('Recurring fields toggled:', disabled);
    }
    
    isRecurringCheckbox.addEventListener('change', toggleRecurringFields);
    toggleRecurringFields(); // Initial state
});
</script>
@endsection