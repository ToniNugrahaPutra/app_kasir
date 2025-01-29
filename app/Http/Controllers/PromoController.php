<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        return view('promo.index');
    }

    public function applyDiscount(Request $request)
    {
        $discountCode = $request->input('discount_code');
        $discount = Promo::where('promo_code', $discountCode)->first();

        if (!$discount) {
            return response()->json(['success' => false, 'message' => 'Kode diskon tidak valid!']);
        }

        if (!$discount->is_active) {
            return response()->json(['success' => false, 'message' => 'Diskon tidak aktif!']);
        }

        if ($discount->start_date > now() || $discount->end_date < now()) {
            return response()->json(['success' => false, 'message' => 'Diskon tidak berlaku pada saat ini!']);
        }

        if ($discount->usage_limit && $discount->usage_count >= $discount->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Diskon sudah mencapai batas penggunaan!']);
        }

        if ($discount->min_transaction && $request->total < $discount->min_transaction) {
            return response()->json(['success' => false, 'message' => 'Total transaksi tidak mencukupi untuk menggunakan diskon ini!']);
        }

        return response()->json(['success' => true, 'message' => 'Diskon berhasil diterapkan!', 'discount' => $discount]);
    }
}
