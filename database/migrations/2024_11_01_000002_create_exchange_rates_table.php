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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3); // Base currency
            $table->string('to_currency', 3);   // Target currency
            $table->decimal('rate', 15, 6);     // Exchange rate
            $table->date('effective_date');
            $table->boolean('is_manual')->default(false); // false = API updated
            $table->timestamps();
            
            $table->foreign('from_currency')->references('code')->on('currencies');
            $table->foreign('to_currency')->references('code')->on('currencies');
            
            $table->unique(['from_currency', 'to_currency', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};