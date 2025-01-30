<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Table;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('owner')) {
            $all = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->where('status', 'paid')
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $today = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->where('status', 'paid')
                                ->whereDate('created_at',Carbon::now())
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $thisMonth = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->where('status', 'paid')
                                ->whereMonth('created_at',Carbon::now()->month)
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
        } else {
            $all = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $today = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->whereDate('created_at',Carbon::now())
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
            $thisMonth = Transaction::with(['transaction_details', 'transaction_details.product'])
                                ->whereMonth('created_at',Carbon::now()->month)
                                ->latest()
                                ->filter(request(['year', 'month']))
                                ->paginate(5);
        }

        return view('transaction.index', [
            'all' => $all,
            'today' => $today,
            'thisMonth' => $thisMonth
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::with(['productPrice', 'category'])->where('outlet_id', session('outlet_id'))->get();
        $categories = Category::where('outlet_id', session('outlet_id'))->get();
        $tables = Table::where('outlet_id', session('outlet_id'))->get();

        return view('transaction.create', compact('customers', 'products', 'categories', 'tables'));
    }

    public function store(Request $request)
    {

        if($request->has('ppn')) {
            $ppn = filter_var($request->ppn, FILTER_SANITIZE_NUMBER_INT);
        }

        $dataProduct = json_decode($request->listProduct);

        try {
            $transaction = new Transaction();
            $transaction->user_id = auth()->user()->id;
            $transaction->outlet_id = $request->outlet_id;
            $transaction->customer_id = $request->customer_id;
            $transaction->total_transaction = $request->total_transaction;
            $transaction->total_payment = 0;
            $transaction->discount_amount = $request->discount_amount ?? null;
            $transaction->ppn = $ppn ?? null;
            $transaction->no_table = $request->table_id ?? null;
            $transaction->status = 'unpaid';
            $transaction->save();

            foreach($dataProduct as $product) {
                $transactionDetail = new TransactionDetail();
                $transactionDetail->transaction_id = $transaction->id;
                $transactionDetail->product_id = $product->id;
                $transactionDetail->qty = $product->qty;
                $transactionDetail->price = $product->price;
                $transactionDetail->save();
            }

            $transaction->total_transaction = $transaction->transaction_details->sum(function($detail) {
                return $detail->price * $detail->qty;
            });
            $transaction->save();

            return redirect()->route('transaction.index')->with('success', 'New transaction successfully created !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('transaction.index')->with('success', 'New transaction successfully created !');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        if (!$user->hasRole('owner')) {
            return redirect()->back();
        }

        return view('transaction.show', [
            'data' => $transaction->with(['transaction_details','transaction_details.menu','user'])->where('id', '=', $transaction->id)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {

        $validateddata = $request->validate([
            'total_transaction' => 'required|numeric',
            'total_payment' => 'required|numeric|gte:total_transaction'
        ]);

        $validateddata["total_payment"] = filter_var($request->total_payment, FILTER_SANITIZE_NUMBER_INT);
        $validateddata["status"] = 'paid';

        Transaction::where('id', $transaction->id)
                    ->update($validateddata);

        return redirect('/transaction')->with('success', 'transaction successfully !');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }


}
