<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::where('is_active', true)->get();
    
        return view('promo.index', compact('promos'));
    }
    

    public function store(Request $request)
    {
        if ($request->start_date > now()) {
            $is_active = false;
        } else {
            $is_active = true;
        }


        $promo = Promo::create([
            'promo_code' => $request->promo_code,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_transaction' => $request->min_transaction,
            'usage_limit' => $request->usage_limit,
            'usage_count' => 0, 
            'is_active' => $is_active,
        ]);
        

        return redirect()->route('promo.index')->with('success', 'Promo berhasil ditambahkan!');
    }
    public function applyDiscount(Request $request)
    {
        $discountCode = $request->input('discount_code');
        $discount = Promo::where('promo_code', $discountCode)->first();
        $total = $request->input('total');

        if($discount){
            if($discount->is_active == 0){
                return response()->json(['success' => false, 'message' => 'Diskon tidak aktif!']);
            }

            if($discount->start_date > now() || $discount->end_date < now()){
                return response()->json(['success' => false, 'message' => 'Diskon tidak berlaku pada saat ini!']);
            }

            if($discount->usage_limit && $discount->usage_count >= $discount->usage_limit){
                return response()->json(['success' => false, 'message' => 'Diskon sudah mencapai batas penggunaan!']);
            }

            if($discount->min_transaction && $total < $discount->min_transaction){
                return response()->json(['success' => false, 'message' => 'Total transaksi tidak mencukupi untuk menggunakan diskon ini!']);
            }

            if($discount->type == 'percentage'){
                $discountAmount = $total * ($discount->value / 100);
            }else{
                $discountAmount = $discount->value;
            }
            $totalPrice = $total - $discountAmount;

            return response()->json([
                'success' => true,
                'message' => 'Diskon berhasil diterapkan!',
                'discount' => $discount,
                'totalPrice' => $totalPrice,
                'discountAmount' => $discountAmount
            ]);
        }else{
            return response()->json(['success' => false, 'message' => 'Kode diskon tidak valid!']);
        }
    }

    public function getActivePromos()
    {
        $promos = Promo::where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->get();

        if ($promos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada promo yang aktif saat ini.']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promo aktif berhasil ditemukan!',
            'promos' => $promos
        ]);
    }
}
