<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = '';
        $profitReport = [];

        if ($user->hasRole('owner')) {
            // Filter transaksi berdasarkan permintaan
            $query = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->where('status', 'paid');
            
            if ($request->data == 'all') {
                $data = $query->latest()->get();
            } elseif ($request->data == 'today') {
                $data = $query->whereDate('created_at', Carbon::now())->latest()->get();
            } elseif ($request->data == 'thisMonth') {
                $data = $query->whereMonth('created_at', Carbon::now()->month)->latest()->get();
            } elseif ($request->month) {
                $data = $query->whereMonth('created_at', $request->month)->latest()->get();
            } elseif ($request->year) {
                $data = $query->whereYear('created_at', $request->year)->latest()->get();
            } else {
                $data = $query->whereMonth('created_at', $request->month)
                              ->whereYear('created_at', $request->year)
                              ->latest()
                              ->get();
            }

            // Hitung laporan laba-rugi
            $profitReport = $this->calculateProfitReport($data);
        } else {
            // Non-owner logic (jika ada)
        }
        return view('transaction.report', [
            'data' => $data,
            'profitReport' => $profitReport
        ]);
    }

    private function calculateProfitReport($transactions)
    {
        $totalRevenue = 0;
        $totalCost = 0;

        foreach ($transactions as $transaction) {
            $totalRevenue += $transaction->total_transaction; // Use transaction total
            foreach ($transaction->transaction_details as $detail) {
                $totalCost += $detail->qty * $detail->product->purchase_price;
            }
        }

        // Return array containing all financial details
        return [
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'profit' => $totalRevenue - $totalCost
        ];
    }

    }

