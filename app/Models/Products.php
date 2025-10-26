<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Variants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'category_id',
        'product_description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(Variants::class, 'product_id', 'product_id');
    }

    public function transactionItems() {
        return $this->hasMany(transactionItems::class, 'product_id', 'product_id');
    }
}
