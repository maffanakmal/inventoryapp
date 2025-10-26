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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id('transaction_item_id');

            $table->foreignId('transaction_id')
                ->constrained('transactions', 'transaction_id')
                ->cascadeOnDelete() 
                ->cascadeOnUpdate();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products', 'product_id')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('variant_id')
                ->constrained('variants', 'variant_id')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('product_name_snapshot');
            $table->string('variant_name_snapshot');
            $table->string('sku_snapshot');
            $table->decimal('unit_price_snapshot', 15, 2);

            $table->string('batch_number')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['transaction_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
