<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\PriceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('outlet_id', session('outlet_id'))->get();
        $menus = Product::with('category')->where('outlet_id', session('outlet_id'))->get();

        return view('menu.index', compact('categories', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();
        return view('menu.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateddata = $request->validate([
           'name' => 'required|min:3',
           'modal' => 'required|regex:/([0-9]+[.,]*)+/',
           'price' => 'required|regex:/([0-9]+[.,]*)+/|gte:modal',
           'category' => 'required',
           'image' => 'required|image|file|max:3048',
           'description' => 'required'
        ]);


        $validateddata["modal"] = filter_var($request->modal, FILTER_SANITIZE_NUMBER_INT);
        $validateddata["price"] = filter_var($request->price, FILTER_SANITIZE_NUMBER_INT);
        $validateddata["picture"] = Storage::disk('public')->put('menu', $request->file('image'));

        Menu::create($validateddata);

        $activity = [
            'user_id' => Auth::id(),
            'action' => 'added a menu '.strtolower($request->name)
        ];

        ActivityLog::create($activity);
        return redirect('/menu')->with('success','New menu has been added !');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productPrice = ProductPrice::where('product_id', $id)->first()->where('price_category_id', 1)->first()->price;
        $product = Product::with('category')->find($id);
        $product->tanggalDibuat = $product->created_at->format('d F Y');
        $product->price = number_format($productPrice, 0, ',', '.');
        return response()->json($product);
    }

    public function edit($id)
    {
        $outletId = session('outlet_id');
        $product = Product::where('id', $id)->where('outlet_id', $outletId)->first();
        $categories = Category::where('outlet_id', $outletId)->get();

        $umumPrice = ProductPrice::where('product_id', $product->id)->where('price_category_id', 1)->first()->price ?? '0';
        $memberPrice = ProductPrice::where('product_id', $product->id)->where('price_category_id', 3)->first()->price ?? '0';
        $umumGrosir = ProductPrice::where('product_id', $product->id)->where('price_category_id', 2)->get();
        $memberGrosir = ProductPrice::where('product_id', $product->id)->where('price_category_id', 4)->get();

        $priceCategories = PriceCategory::all();

        if(auth()->user()->hasRole('owner')){
            return view('menu.edit', compact('product', 'categories', 'priceCategories', 'umumPrice', 'memberPrice', 'umumGrosir', 'memberGrosir'));
        }
        return redirect()->back();
    }

    public function detail($id)
    {
        $product = Product::with(['productPrice' => function($query) {
            $query->orderBy('price_category_id', 'asc');
        }])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'image' => $product->image,
            'description' => $product->description,
            'product_price' => $product->productPrice->map(function($price) {
                return [
                    'id' => $price->id,
                    'price_category_id' => $price->price_category_id,
                    'price' => $price->price,
                    'min_quantity' => $price->min_quantity
                ];
            })
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'purchase_price' => 'required|regex:/([0-9]+[.,]*)+/',
            'category_id' => 'nullable',
            'umumPrice' => 'required|regex:/([0-9]+[.,]*)+/',
            'memberPrice' => 'nullable|regex:/([0-9]+[.,]*)+/',
            'grosir-umum' => 'nullable|array',
            'grosir-member' => 'nullable|array',
            'image' => 'nullable|image|file|max:3048',
            'description' => 'nullable'
        ]);

        function formatNumber($number){
            return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
        }

        try {
            DB::beginTransaction();

            $product = Product::find($id);

            $filename = $product->image;
            if ($request->file('image')) {
                Storage::disk('public')->delete('products/'.$product->image);
                $file = $request->file('image');
                $filename = $product->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('products', $request->file('image'), $filename);
            }

            Product::where('id', $product->id)
                ->update([
                    'name' => $request->name,
                    'image' => $filename,
                    'description' => $request->description,
                    'purchase_price' => formatNumber($request->purchase_price),
                    'category_id' => $request->category_id,
                ]);

            ProductPrice::where('product_id', $product->id)
                ->where('price_category_id', 1)
                ->update([
                    'price' => formatNumber($request->umumPrice),
                    'min_quantity' => 1,
                ]);

            if($request->has('memberPrice') && $request->memberPrice != 0){
                ProductPrice::where('product_id', $product->id)
                ->where('price_category_id', 3)
                ->delete();

                ProductPrice::create([
                    'product_id' => $product->id,
                    'price_category_id' => 3,
                    'price' => formatNumber($request->memberPrice),
                    'min_quantity' => 1,
                ]);
            }else{
                ProductPrice::where('product_id', $product->id)
                    ->where('price_category_id', 3)
                    ->delete();
            }

            if($request->has('grosir-umum') && is_array($request->input('grosir-umum'))){
                ProductPrice::where('product_id', $product->id)
                    ->where('price_category_id', 2)
                    ->delete();

                foreach($request->input('grosir-umum') as $grosir){
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'price_category_id' => 2,
                        'price' => formatNumber($grosir['price']),
                        'min_quantity' => $grosir['min_quantity'],
                    ]);
                }
            }else{
                ProductPrice::where('product_id', $product->id)
                    ->where('price_category_id', 2)
                    ->delete();
            }

            if($request->has('grosir-member') && is_array($request->input('grosir-member'))){
                ProductPrice::where('product_id', $product->id)
                    ->where('price_category_id', 4)
                    ->delete();

                foreach($request->input('grosir-member') as $grosir){
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'price_category_id' => 4,
                        'price' => formatNumber($grosir['price']),
                        'min_quantity' => $grosir['min_quantity'],
                    ]);
                }
            }else{
                ProductPrice::where('product_id', $product->id)
                    ->where('price_category_id', 4)
                    ->delete();
            }

            $activity = [
                'user_id' => Auth::id(),
                'action' => 'edited a product '. strtolower($product->name)
            ];

            ActivityLog::create($activity);
            DB::commit();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage());
        }

        return redirect('/menu')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        Storage::delete($product->picture);
        $product->destroy($product->id);
        $activity = [
            'user_id' => Auth::id(),
            'action' => 'deleted a product '.strtolower($product->name)
        ];
        ActivityLog::create($activity);
        return redirect('/menu');
    }

}

