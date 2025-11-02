<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Product Performance</h5>
    </div>
    <div class="card-body">
        @if(isset($productPerformanceData))
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-primary">Total Products</h6>
                        <h3 class="text-primary">{{ $productPerformanceData['total_products'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-success">Total Units Sold</h6>
                        <h3 class="text-success">{{ $productPerformanceData['total_units_sold'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h6 class="text-info">Total Revenue</h6>
                        <h4 class="text-info">${{ number_format($productPerformanceData['total_revenue'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h6>Top Performing Products</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                                <th>Profit Margin</th>
                                <th>Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productPerformanceData['top_products'] ?? [] as $product)
                                <tr>
                                    <td>{{ $product['name'] ?? 'N/A' }}</td>
                                    <td>{{ $product['category'] ?? 'N/A' }}</td>
                                    <td>{{ $product['units_sold'] ?? 0 }}</td>
                                    <td>${{ number_format($product['revenue'] ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ ($product['profit_margin'] ?? 0) >= 20 ? 'bg-success' : (($product['profit_margin'] ?? 0) >= 10 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($product['profit_margin'] ?? 0, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($product['stock_quantity'] ?? 0) > ($product['reorder_level'] ?? 0) ? 'bg-success' : 'bg-danger' }}">
                                            {{ ($product['stock_quantity'] ?? 0) > ($product['reorder_level'] ?? 0) ? 'In Stock' : 'Low Stock' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h6>Product Category Performance</h6>
                <div class="row">
                    @foreach($productPerformanceData['category_performance'] ?? [] as $category)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $category['name'] ?? 'Unknown Category' }}</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted">Products</small>
                                            <h5>{{ $category['product_count'] ?? 0 }}</h5>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Revenue</small>
                                            <h5>${{ number_format($category['revenue'] ?? 0, 0) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Low Stock Alerts</h6>
                        @if(($productPerformanceData['low_stock_products'] ?? []))
                            <div class="table-responsive">
                                <table class="table table-sm table-warning">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Current Stock</th>
                                            <th>Reorder Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productPerformanceData['low_stock_products'] as $product)
                                            <tr>
                                                <td>{{ $product['name'] ?? 'N/A' }}</td>
                                                <td>{{ $product['stock_quantity'] ?? 0 }}</td>
                                                <td>{{ $product['reorder_level'] ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> All products are well stocked!
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6>Fast Moving Products</h6>
                        @if(($productPerformanceData['fast_moving'] ?? []))
                            <div class="table-responsive">
                                <table class="table table-sm table-success">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Units Sold</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productPerformanceData['fast_moving'] as $product)
                                            <tr>
                                                <td>{{ $product['name'] ?? 'N/A' }}</td>
                                                <td>{{ $product['units_sold'] ?? 0 }}</td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-up"></i> Growing
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Performance data being calculated...
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-muted">
                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                <p>Product performance data will be displayed here</p>
            </div>
        @endif
    </div>
</div>
