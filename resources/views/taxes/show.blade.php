@extends('layouts.app')

@section('title', 'Tax Rate: ' . $tax->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-percentage"></i> {{ $tax->name }}</h1>
                <div class="btn-group">
                    <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tax Rates
                    </a>
                    <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Tax Rate
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Tax Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tax Rate Details</h5>
                                <span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }} fs-6">
                                    {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Tax Name:</dt>
                                        <dd class="col-sm-8"><strong>{{ $tax->name }}</strong></dd>
                                        
                                        <dt class="col-sm-4">Tax Code:</dt>
                                        <dd class="col-sm-8"><code>{{ $tax->code }}</code></dd>
                                        
                                        <dt class="col-sm-4">Rate:</dt>
                                        <dd class="col-sm-8"><strong>{{ $tax->formatted_rate }}</strong></dd>
                                        
                                        <dt class="col-sm-4">Type:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-{{ $tax->type === 'percentage' ? 'info' : 'warning' }}">
                                                {{ ucfirst($tax->type) }}
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Created:</dt>
                                        <dd class="col-sm-7">{{ $tax->created_at->format('M d, Y') }}</dd>
                                        
                                        <dt class="col-sm-5">Updated:</dt>
                                        <dd class="col-sm-7">{{ $tax->updated_at->format('M d, Y') }}</dd>
                                        
                                        @if($tax->products->count() > 0)
                                            <dt class="col-sm-5">Products:</dt>
                                            <dd class="col-sm-7">{{ $tax->products->count() }} items</dd>
                                        @endif
                                        
                                        @if($tax->services->count() > 0)
                                            <dt class="col-sm-5">Services:</dt>
                                            <dd class="col-sm-7">{{ $tax->services->count() }} items</dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if($tax->description)
                                <div class="mt-4">
                                    <h6 class="text-muted">Description</h6>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($tax->description)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Usage Examples -->
                            <div class="mt-4">
                                <h6 class="text-muted">Calculation Examples</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <h6>Example 1</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Base Amount:</td>
                                                    <td class="text-end">$100.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax ({{ $tax->formatted_rate }}):</td>
                                                    <td class="text-end">
                                                        ${{ number_format($tax->calculateTax(100), 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td><strong>Total:</strong></td>
                                                    <td class="text-end">
                                                        <strong>${{ number_format(100 + $tax->calculateTax(100), 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <h6>Example 2</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Base Amount:</td>
                                                    <td class="text-end">$500.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax ({{ $tax->formatted_rate }}):</td>
                                                    <td class="text-end">
                                                        ${{ number_format($tax->calculateTax(500), 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td><strong>Total:</strong></td>
                                                    <td class="text-end">
                                                        <strong>${{ number_format(500 + $tax->calculateTax(500), 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Tax Rate
                                </a>
                                
                                <form method="POST" action="{{ route('taxes.toggle', $tax) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-{{ $tax->is_active ? 'warning' : 'success' }} w-100"
                                            onclick="return confirm('{{ $tax->is_active ? 'Deactivate' : 'Activate' }} this tax rate?')">
                                        <i class="fas fa-{{ $tax->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $tax->is_active ? 'Deactivate Tax Rate' : 'Activate Tax Rate' }}
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('taxes.destroy', $tax) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Delete this tax rate? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete Tax Rate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Statistics -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Usage Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $tax->products->count() }}</h4>
                                    <small class="text-muted">Products</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info">{{ $tax->services->count() }}</h4>
                                    <small class="text-muted">Services</small>
                                </div>
                            </div>
                            
                            @if($tax->products->count() > 0 || $tax->services->count() > 0)
                                <hr>
                                <p class="text-muted small mb-0">
                                    This tax rate cannot be deleted as it is being used by products or services.
                                </p>
                            @else
                                <hr>
                                <p class="text-success small mb-0">
                                    This tax rate is not currently being used and can be safely deleted.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items -->
            @if($tax->products->count() > 0 || $tax->services->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Items Using This Tax Rate</h5>
                            </div>
                            <div class="card-body">
                                @if($tax->products->count() > 0)
                                    <h6>Products ({{ $tax->products->count() }})</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tax->products->take(10) as $product)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                                                {{ $product->name }}
                                                            </a>
                                                        </td>
                                                        <td><code>{{ $product->sku }}</code></td>
                                                        <td>${{ number_format($product->unit_price, 2) }}</td>
                                                        <td>{{ $product->stock_quantity }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($tax->products->count() > 10)
                                            <small class="text-muted">
                                                Showing 10 of {{ $tax->products->count() }} products
                                            </small>
                                        @endif
                                    </div>
                                @endif

                                @if($tax->services->count() > 0)
                                    <h6>Services ({{ $tax->services->count() }})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Category</th>
                                                    <th>Rate</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tax->services->take(10) as $service)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('services.show', $service) }}" class="text-decoration-none">
                                                                {{ $service->name }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $service->category->name ?? 'N/A' }}</td>
                                                        <td>${{ number_format($service->hourly_rate, 2) }}/hour</td>
                                                        <td>
                                                            <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                                                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($tax->services->count() > 10)
                                            <small class="text-muted">
                                                Showing 10 of {{ $tax->services->count() }} services
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
