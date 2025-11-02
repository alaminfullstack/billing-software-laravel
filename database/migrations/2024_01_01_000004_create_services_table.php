<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('hourly_rate', 10, 2);
            $table->integer('duration')->default(1);
            $table->enum('duration_unit', ['hours', 'days', 'sessions'])->default('hours');
            $table->decimal('basic_price', 10, 2)->nullable();
            $table->decimal('standard_price', 10, 2)->nullable();
            $table->decimal('premium_price', 10, 2)->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_bookable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
