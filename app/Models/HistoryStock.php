<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_stock_id';

    protected $fillable = [
        'variant_id',
        'transaction_id',
        'transaction_type',
        'input_quantity',
        'output_quantity',
        'balance_quantity',
        'officer',
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variants::class, 'variant_id', 'variant_id');
    }
}
