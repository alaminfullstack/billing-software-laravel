<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add currency support to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('notes');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000)->after('currency_code');
            $table->decimal('base_currency_amount', 15, 2)->nullable()->after('exchange_rate');
            
            $table->foreign('currency_code')->references('code')->on('currencies');
        });

        // Add currency support to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('notes');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000)->after('currency_code');
            $table->decimal('base_currency_amount', 15, 2)->nullable()->after('exchange_rate');
            
            $table->foreign('currency_code')->references('code')->on('currencies');
        });

        // Add currency support to expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('notes');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000)->after('currency_code');
            $table->decimal('base_currency_amount', 15, 2)->nullable()->after('exchange_rate');
            
            $table->foreign('currency_code')->references('code')->on('currencies');
        });

        // Create financial statement accounts table for balance sheet
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // Asset code like 1000, 2000, etc.
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('category'); // Current Asset, Fixed Asset, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create account balances table for trial balance and balance sheet
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_account_id')->constrained('financial_accounts');
            $table->date('balance_date');
            $table->decimal('debit_balance', 15, 2)->default(0);
            $table->decimal('credit_balance', 15, 2)->default(0);
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('base_currency_debit', 15, 2)->default(0);
            $table->decimal('base_currency_credit', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('currency_code')->references('code')->on('currencies');
            
            $table->unique(['financial_account_id', 'balance_date']);
        });

        // Create cash flow transactions
        Schema::create('cash_flow_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('type', ['operating', 'investing', 'financing']);
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('base_currency_amount', 15, 2);
            // $table->morphs('transactionable'); // Can relate to invoice, payment, expense, etc.
            // Use manual morphs with shorter index name to avoid MySQL limit
           
            // $table->index(['transactionable_type', 'transactionable_id'], 'cf_transactable_idx');
            $table->timestamps();
            
            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flow_transactions');
        Schema::dropIfExists('account_balances');
        Schema::dropIfExists('financial_accounts');
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_currency_amount']);
        });
        
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_currency_amount']);
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'exchange_rate', 'base_currency_amount']);
        });
    }
};