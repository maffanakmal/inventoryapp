<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncreasedPrices extends Model
{
    use HasFactory;

    protected $primaryKey = 'increase_price_id';

    protected $fillable = [
        'variant_id',
        'transaction_id',
        'transaction_item_id',
        'old_price',
        'new_price',
        'increase_amount',
        'is_confirmed',
    ];

    public function variant()
    {
        return $this->belongsTo(Variants::class, 'variant_id', 'variant_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItems::class, 'transaction_item_id', 'transaction_item_id');
    }
}
