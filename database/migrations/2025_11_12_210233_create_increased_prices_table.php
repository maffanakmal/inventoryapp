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
        Schema::create('increased_prices', function (Blueprint $table) {
            $table->id('increase_price_id');

            $table->foreignId('variant_id')
                ->constrained('variants', 'variant_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained('transactions', 'transaction_id')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('transaction_item_id')
                ->nullable()
                ->constrained('transaction_items', 'transaction_item_id')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->decimal('increase_amount', 10, 2);
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('increased_prices');
    }
};
