<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\FinancialDashboardController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\CurrencyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Financial Dashboard
    Route::get('/financial-dashboard', [FinancialDashboardController::class, 'index'])->name('financial-dashboard');
    
    // Profile management
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AuthController::class, 'changePassword'])->name('password.change');
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::patch('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/payments/create/{invoice}', [PaymentController::class, 'create'])->name('payments.create-for-invoice');
    
    // Products and Services
    Route::get('/products', [ProductController::class, 'products'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [ProductController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'showProduct'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroyProduct'])->name('products.destroy');
    
    Route::get('/services', [ProductController::class, 'services'])->name('services.index');
    Route::get('/services/create', [ProductController::class, 'createService'])->name('services.create');
    Route::post('/services', [ProductController::class, 'storeService'])->name('services.store');
    Route::get('/services/{service}', [ProductController::class, 'showService'])->name('services.show');
    Route::get('/services/{service}/edit', [ProductController::class, 'editService'])->name('services.edit');
    Route::put('/services/{service}', [ProductController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [ProductController::class, 'destroyService'])->name('services.destroy');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class);
    
    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::get('/expenses/{expense}/download-receipt', [ExpenseController::class, 'downloadReceipt'])->name('expenses.download-receipt');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportsController::class, 'revenueReport'])->name('revenue');
        Route::get('/expenses', [ReportsController::class, 'expenseReport'])->name('expenses');
        Route::get('/profit-loss', [ReportsController::class, 'profitLossReport'])->name('profit-loss');
        Route::get('/customers', [ReportsController::class, 'customerReport'])->name('customers');
        Route::get('/invoice-status', [ReportsController::class, 'invoiceStatusReport'])->name('invoice-status');
    });
    
    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::patch('/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::patch('/quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('quotes.accept');
    Route::patch('/quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');
    Route::get('/quotes/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert-to-invoice');
    
    // Taxes
    Route::resource('taxes', TaxController::class);
    Route::patch('/taxes/{tax}/toggle', [TaxController::class, 'toggle'])->name('taxes.toggle');
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::patch('/suppliers/{supplier}/toggle', [SupplierController::class, 'toggle'])->name('suppliers.toggle');
    
    // Inventory Transactions
    Route::resource('inventory-transactions', InventoryTransactionController::class);
    Route::get('/api/products/{product}/stock-history', [InventoryTransactionController::class, 'getStockHistory'])->name('api.products.stock-history');
    Route::get('/api/low-stock-products', [InventoryTransactionController::class, 'getLowStockProducts'])->name('api.low-stock-products');
    
    // Financial Reports
    Route::prefix('financial-reports')->name('financial-reports.')->group(function () {
        Route::get('/', [FinancialReportsController::class, 'index'])->name('index');
        Route::get('/balance-sheet', [FinancialReportsController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/cash-flow', [FinancialReportsController::class, 'cashFlowStatement'])->name('cash-flow');
        Route::get('/trial-balance', [FinancialReportsController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/aging-receivables', [FinancialReportsController::class, 'agingReceivables'])->name('aging-receivables');
        Route::get('/aging-payables', [FinancialReportsController::class, 'agingPayables'])->name('aging-payables');
        Route::get('/tax-report', [FinancialReportsController::class, 'taxReport'])->name('tax-report');
        Route::get('/profit-loss', [FinancialReportsController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/dashboard', [FinancialReportsController::class, 'index'])->name('dashboard');
    });
    
    // Currency Management
    Route::prefix('currencies')->name('currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
        
        // Exchange Rates
        Route::get('/exchange-rates', [CurrencyController::class, 'exchangeRates'])->name('exchange-rates');
        Route::get('/update-exchange-rates', [CurrencyController::class, 'updateExchangeRates'])->name('update-exchange-rates');
        Route::get('/edit-exchange-rate', [CurrencyController::class, 'editExchangeRate'])->name('edit-exchange-rate');
        Route::post('/update-exchange-rate', [CurrencyController::class, 'updateExchangeRate'])->name('update-exchange-rate');
        
        // Currency Operations
        Route::post('/convert', [CurrencyController::class, 'convert'])->name('convert');
        Route::get('/historical-rates', [CurrencyController::class, 'historicalRates'])->name('historical-rates');
        
        // Settings
        Route::get('/settings', [CurrencyController::class, 'settings'])->name('settings');
        Route::post('/settings', [CurrencyController::class, 'updateSettings'])->name('update-settings');
        
        // Utility
        Route::get('/seed-defaults', [CurrencyController::class, 'seedDefaults'])->name('seed-defaults');
    });
});
