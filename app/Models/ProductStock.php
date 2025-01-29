<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'outlet_id',
        'stock'
    ];

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    // Menambahkan relasi ke StockMovement untuk menghitung stok masuk dan keluar
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }

    // Fungsi untuk menghitung total stok masuk
    public function totalStockIn()
    {
        return $this->stockMovements()->where('movement_type', 'in')->sum('quantity_change');
    }

    // Fungsi untuk menghitung total stok keluar
    public function totalStockOut()
    {
        return $this->stockMovements()->where('movement_type', 'out')->sum('quantity_change');
    }

    // Fungsi untuk menghitung sisa stok
    public function remainingStock()
    {
        return $this->stock - $this->totalStockOut() + $this->totalStockIn();
    }
}

