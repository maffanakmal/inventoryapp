<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'transaction_code',
        'transaction_type',
        'total_items',
        'total_price',
        'notes',
        'officer',
        'supplier',
        'customer',
        'contact',
    ];

    public function historyStocks()
    {
        return $this->hasMany(HistoryStock::class, 'transaction_id', 'transaction_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItems::class, 'transaction_id', 'transaction_id');
    }
}
