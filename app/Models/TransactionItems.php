<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItems extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_item_id';

    protected $fillable = [
        'transaction_id',
        'variant_id',
        'product_id',
        'product_name_snapshot',
        'variant_name_snapshot',
        'sku_snapshot',
        'unit_price_snapshot',
        'batch_number',
        'quantity',
        'price',
        'total',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variants::class, 'variant_id', 'variant_id');
    }

    public function increasedPrices()
    {
        return $this->hasMany(IncreasedPrices::class, 'transaction_items_id', 'transaction_items_id');
    }
}
