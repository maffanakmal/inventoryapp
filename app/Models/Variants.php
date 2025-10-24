<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variants extends Model
{
    use HasFactory;

    protected $primaryKey = 'variant_id';

    protected $fillable = [
        'product_id',
        'sku',
        'variant_name',
        'variant_image',
        'variant_price',
        'stock_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
