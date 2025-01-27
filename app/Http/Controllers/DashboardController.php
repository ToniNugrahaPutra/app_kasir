<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home', [
            "total_menus" => Product::where('outlet_id', session('outlet_id'))->count(),
            'total_sales' => Transaction::select(Transaction::raw('SUM(total_transaction) as total_sales'))->whereDate('created_at', NOW()->toDateString())->get(),
            'total_income' => Transaction::select(Transaction::raw('SUM(total_payment) as total_income'))->whereDate('created_at', NOW()->toDateString())->get(),
            'invoice' => Transaction::select(Transaction::raw('COUNT(id) as total_invoice'))->whereDate('created_at', NOW()->toDateString())->get(),
            'cashier' => User::select(User::raw('COUNT(id) as cashier'))->whereHas('roles', function($query) {
                $query->where('name', 'cashier');
            })->get(),
            'total_user' => User::select(User::raw('COUNT(id) as total_user'))->get(),
            'total_paid' => Transaction::select(Transaction::raw('COUNT(id) as total_paid'))->where('status','paid')->get(),
            'total_unpaid' => Transaction::select(Transaction::raw('COUNT(id) as total_unpaid'))->where('status','unpaid')->get(),
            'tables' => Transaction::select(Transaction::raw('COUNT(no_table) as tables'))->where('status','unpaid')->get()
        ]);
    }

    public function chooseOutlet(Request $request)
    {
        if($request->ajax()){
            //simpan outlet_id ke session
            session(['outlet_id' => $request->outlet_id]);
            return response()->json(['success' => true]);
        }
        $outlets = auth()->user()->outlets;
        return view('choose-outlet', compact('outlets'));
    }
}

