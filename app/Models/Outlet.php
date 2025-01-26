<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'outlet_code', 'slug', 'address', 'phone', 'email', 'logo', 'status'];
}
