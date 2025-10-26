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
        Schema::create('history_stocks', function (Blueprint $table) {
            $table->id('history_stock_id');

            $table->foreignId('variant_id')
                ->constrained('variants', 'variant_id')
                ->restrictOnDelete() 
                ->cascadeOnUpdate();

            $table->foreignId('transaction_id')
                ->nullable()          
                ->constrained('transactions', 'transaction_id')
                ->nullOnDelete()       
                ->cascadeOnUpdate();

            $table->enum('transaction_type', ['in', 'out', 'adjustment', 'return']);
            $table->integer('input_quantity')->default(0);
            $table->integer('output_quantity')->default(0);
            $table->integer('balance_quantity');
            $table->string('officer');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['variant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_stocks');
    }
};
