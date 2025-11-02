<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exchange Rates - Accounting System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .title { font-size: 28px; font-weight: bold; color: #333; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.3s; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-warning:hover { background: #e0a800; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .btn-outline-secondary { background: transparent; color: #6c757d; border: 1px solid #6c757d; }
        .btn-outline-secondary:hover { background: #6c757d; color: white; }
        .nav-links { display: flex; gap: 15px; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .alert-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .card { background: white; border: 1px solid #ddd; border-radius: 8px; margin: 20px 0; overflow: hidden; }
        .card-header { background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #ddd; font-weight: bold; }
        .card-body { padding: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .table tr:hover { background-color: #f5f5f5; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-primary { background: #007bff; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .exchange-rate-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0; }
        .rate-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #fafafa; }
        .rate-pair { font-weight: bold; color: #333; }
        .rate-value { font-size: 18px; color: #007bff; font-weight: bold; }
        .rate-date { font-size: 12px; color: #666; }
        .manual-rate { background: #fff3cd; border-left: 4px solid #ffc107; }
        .api-rate { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        .base-currency { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 8px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Exchange Rates Management</h1>
            <nav class="nav-links">
                <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary">Currency Management</a>
                <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Financial Reports</a>
                <button onclick="showUpdateModal()" class="btn btn-warning">Update from API</button>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value">{{ $exchangeRates->count() }}</div>
                <div class="stat-label">Total Exchange Rates</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $exchangeRates->where('is_manual', false)->count() }}</div>
                <div class="stat-label">API Updated Rates</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $exchangeRates->where('is_manual', true)->count() }}</div>
                <div class="stat-label">Manual Rates</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $baseCurrency->code ?? 'N/A' }}</div>
                <div class="stat-label">Base Currency</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Current Exchange Rates (Latest)</h3>
                <p class="mb-0">All rates are relative to the base currency: {{ $baseCurrency->name }} ({{ $baseCurrency->code }})</p>
            </div>
            <div class="card-body">
                <div class="exchange-rate-grid">
                    @foreach($ratesByPair as $pair => $rates)
                        @php
                            $latestRate = $rates->first();
                            $fromCurrency = $latestRate->fromCurrency;
                            $toCurrency = $latestRate->toCurrency;
                        @endphp
                        <div class="rate-card {{ $latestRate->is_manual ? 'manual-rate' : 'api-rate' }}">
                            <div class="rate-pair">
                                {{ $fromCurrency->code }} → {{ $toCurrency->code }}
                                @if($fromCurrency->is_base_currency)
                                    <span class="base-currency">BASE</span>
                                @endif
                            </div>
                            <div class="rate-value">{{ number_format($latestRate->rate, 6) }}</div>
                            <div class="rate-date">{{ $latestRate->effective_date->format('M d, Y') }}</div>
                            <div style="margin-top: 10px;">
                                @if($latestRate->is_manual)
                                    <span class="badge badge-warning">Manual</span>
                                @else
                                    <span class="badge badge-info">API</span>
                                @endif
                                <span class="badge badge-primary">{{ $fromCurrency->symbol }}{{ number_format(1 / $latestRate->rate, 6) }} {{ $toCurrency->code }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Exchange Rate History</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>From Currency</th>
                                <th>To Currency</th>
                                <th>Rate</th>
                                <th>Effective Date</th>
                                <th>Source</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exchangeRates as $rate)
                            <tr>
                                <td>
                                    <strong>{{ $rate->from_currency }}</strong>
                                    @if($rate->fromCurrency)
                                        <br><small>{{ $rate->fromCurrency->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $rate->to_currency }}</strong>
                                    @if($rate->toCurrency)
                                        <br><small>{{ $rate->toCurrency->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="rate-value">{{ number_format($rate->rate, 6) }}</span>
                                </td>
                                <td>{{ $rate->effective_date->format('M d, Y') }}</td>
                                <td>
                                    @if($rate->is_manual)
                                        <span class="badge badge-warning">Manual</span>
                                    @else
                                        <span class="badge badge-info">API</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('currencies.edit-exchange-rate', [
                                        'from_currency' => $rate->from_currency,
                                        'to_currency' => $rate->to_currency,
                                        'effective_date' => $rate->effective_date->format('Y-m-d')
                                    ]) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4>Update from API</h4>
                        <p>Fetch latest exchange rates from external API providers</p>
                        <button onclick="showUpdateModal()" class="btn btn-warning">Update Now</button>
                    </div>
                    <div>
                        <h4>Historical Rates</h4>
                        <p>View exchange rate history for specific date ranges</p>
                        <a href="#historical" class="btn btn-info">View History</a>
                    </div>
                    <div>
                        <h4>Manual Rate Entry</h4>
                        <p>Set manual exchange rates for specific dates</p>
                        <a href="#manual" class="btn btn-primary" onclick="showManualModal()">Add Manual Rate</a>
                    </div>
                    <div>
                        <h4>Currency Converter</h4>
                        <p>Convert amounts between different currencies</p>
                        <a href="{{ route('currencies.index') }}#convert" class="btn btn-success">Convert Currency</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update from API Modal -->
    <div id="updateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%;">
            <h3>Update Exchange Rates</h3>
            <p>This will fetch the latest exchange rates from our API provider. Continue?</p>
            <div style="margin-top: 20px;">
                <a href="{{ route('currencies.update-exchange-rates') }}" class="btn btn-warning" style="margin-right: 10px;">Update Rates</a>
                <button onclick="hideUpdateModal()" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Manual Rate Modal -->
    <div id="manualModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">
            <h3>Add Manual Exchange Rate</h3>
            <form method="POST" action="{{ route('currencies.update-exchange-rate') }}">
                @csrf
                <div style="margin: 15px 0;">
                    <label>From Currency:</label>
                    <select name="from_currency" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin: 15px 0;">
                    <label>To Currency:</label>
                    <select name="to_currency" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin: 15px 0;">
                    <label>Exchange Rate:</label>
                    <input type="number" name="rate" step="0.000001" min="0.000001" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin: 15px 0;">
                    <label>Effective Date:</label>
                    <input type="date" name="effective_date" value="{{ now()->format('Y-m-d') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Rate</button>
                    <button type="button" onclick="hideManualModal()" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showUpdateModal() {
            document.getElementById('updateModal').style.display = 'block';
        }
        
        function hideUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
        }
        
        function showManualModal() {
            document.getElementById('manualModal').style.display = 'block';
        }
        
        function hideManualModal() {
            document.getElementById('manualModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const updateModal = document.getElementById('updateModal');
            const manualModal = document.getElementById('manualModal');
            if (event.target === updateModal) {
                hideUpdateModal();
            }
            if (event.target === manualModal) {
                hideManualModal();
            }
        }
    </script>
</body>
</html>