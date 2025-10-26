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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('transaction_code')->unique();
            $table->enum('transaction_type', ['in', 'out']);
            $table->integer('total_items');
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->string('officer');
            $table->string('supplier')->nullable();
            $table->string('customer')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
