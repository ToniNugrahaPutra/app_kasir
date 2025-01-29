<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    // Konstruktor untuk middleware
    public function __construct()
    {
        $this->middleware('role:gudang'); // Pastikan hanya Gudang yang bisa mengakses
    }

    public function index()
    {
        // Ambil data produk dan stok produk berdasarkan outlet_id
        $products = Product::all(); // Sesuaikan dengan outlet jika diperlukan
        $productStocks = ProductStock::where('outlet_id', session('outlet_id'))->get();  // Ambil stok berdasarkan outlet
    
        // Hitung total stok
        $total_products = $products->count();
        
        // Ambil total stok masuk dan keluar
        $total_stock_in = $productStocks->sum(function($productStock) {
            return $productStock->totalStockIn(); // Ambil total stok masuk per produk
        });
    
        $total_stock_out = $productStocks->sum(function($productStock) {
            return $productStock->totalStockOut(); // Ambil total stok keluar per produk
        });
    
        // Kirim data ke view
        return view('dashboard.gudang', compact('products', 'total_products', 'total_stock_in', 'total_stock_out', 'productStocks'));
    }
    
    // Menampilkan detail stok produk
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $productStock = ProductStock::where('product_id', $id)->first();
        
        return view('dashboard.gudang', compact('product', 'productStock'));
    }

    // Mengelola pergerakan stok (misalnya stok masuk atau keluar)
    public function manageStock(Request $request)
    {
        // Validasi input pergerakan stok
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_change' => 'required|integer',
            'movement_type' => 'required|in:in,out', // jenis pergerakan: masuk atau keluar
        ]);

        // Menyimpan pergerakan stok
        $movement = new StockMovement();
        $movement->product_id = $request->product_id;
        $movement->quantity_change = $request->quantity_change;
        $movement->movement_type = $request->type; // "in" untuk stok masuk, "out" untuk stok keluar
        $movement->user_id = auth()->user()->id;
        $movement->save();

        // Update stok produk
        $productStock = ProductStock::where('product_id', $request->product_id)->first();
        
        if ($request->movement_type == 'in') {
            $productStock->stock += $request->quantity_change; // Menambahkan stok
        } else {
            $productStock->stock -= $request->quantity_change; // Mengurangi stok
        }
        
        $productStock->save();

        return redirect()->route('dashboard.gudang')->with('success', 'Pergerakan stok berhasil.');

    }
    
}
