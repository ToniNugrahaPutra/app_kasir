<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_code',
        'description',
        'type',
        'value',
        'start_date',
        'end_date',
        'min_transaction',
        'usage_limit',
        'usage_count',
        'is_active'
    ];
}
