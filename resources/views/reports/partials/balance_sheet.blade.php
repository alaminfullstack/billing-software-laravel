<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-balance-scale"></i> Balance Sheet Summary</h5>
    </div>
    <div class="card-body">
        @if(isset($balanceSheetData))
            <div class="row">
                <!-- Assets -->
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Assets</h6>
                        </div>
                        <div class="card-body">
                            @foreach($balanceSheetData['assets'] ?? [] as $asset => $amount)
                                <div class="d-flex justify-content-between">
                                    <span>{{ ucwords(str_replace('_', ' ', $asset)) }}</span>
                                    <span class="fw-bold">${{ number_format($amount, 2) }}</span>
                                </div>
                                <hr>
                            @endforeach
                            <div class="d-flex justify-content-between">
                                <strong>Total Assets</strong>
                                <strong class="text-primary">${{ number_format($balanceSheetData['total_assets'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liabilities -->
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">Liabilities</h6>
                        </div>
                        <div class="card-body">
                            @foreach($balanceSheetData['liabilities'] ?? [] as $liability => $amount)
                                <div class="d-flex justify-content-between">
                                    <span>{{ ucwords(str_replace('_', ' ', $liability)) }}</span>
                                    <span class="fw-bold">${{ number_format($amount, 2) }}</span>
                                </div>
                                <hr>
                            @endforeach
                            <div class="d-flex justify-content-between">
                                <strong>Total Liabilities</strong>
                                <strong class="text-warning">${{ number_format($balanceSheetData['total_liabilities'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equity -->
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Equity</h6>
                        </div>
                        <div class="card-body">
                            @foreach($balanceSheetData['equity'] ?? [] as $equityItem => $amount)
                                <div class="d-flex justify-content-between">
                                    <span>{{ ucwords(str_replace('_', ' ', $equityItem)) }}</span>
                                    <span class="fw-bold">${{ number_format($amount, 2) }}</span>
                                </div>
                                <hr>
                            @endforeach
                            <div class="d-flex justify-content-between">
                                <strong>Total Equity</strong>
                                <strong class="text-success">${{ number_format($balanceSheetData['total_equity'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert {{ ($balanceSheetData['balance'] ?? 0) == 0 ? 'alert-success' : 'alert-danger' }}">
                        <h6 class="alert-heading">Balance Check</h6>
                        <p class="mb-0">
                            Assets = Liabilities + Equity: 
                            <strong>${{ number_format($balanceSheetData['total_assets'] ?? 0, 2) }}</strong> 
                            {{ ($balanceSheetData['balance'] ?? 0) == 0 ? '=' : '≠' }} 
                            <strong>${{ number_format(($balanceSheetData['total_liabilities'] ?? 0) + ($balanceSheetData['total_equity'] ?? 0), 2) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-muted">
                <i class="fas fa-balance-scale fa-3x mb-3"></i>
                <p>Balance sheet data will be displayed here</p>
            </div>
        @endif
    </div>
</div>
