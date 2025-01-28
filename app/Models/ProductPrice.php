<?php

namespace App\Models;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\PriceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'price_category_id', 'price', 'min_quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id', 'id');
    }
}
