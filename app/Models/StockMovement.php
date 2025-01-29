<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity_change',
        'movement_type',
        'user_id',
        'outlet_id',  // Tambahkan outlet_id untuk melacak pergerakan stok per outlet
    ];

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relasi ke user (petugas yang melakukan pergerakan stok)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke outlet (pergerakan stok per outlet)
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
