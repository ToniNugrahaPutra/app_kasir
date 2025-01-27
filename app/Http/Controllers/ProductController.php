<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\PriceCategory;
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
    public function show(Request $request, Menu $menu)
    {
        $id = $request->id;
        $menu = $menu->find($id);
        $menu->diff = $menu->created_at->diffForHumans();;
        return $menu;
    }

    public function edit($id)
    {
        $outletId = session('outlet_id');
        $product = Product::where('id', $id)->where('outlet_id', $outletId)->first();
        $categories = Category::where('outlet_id', $outletId)->get();
        $productPrice = ProductPrice::where('product_id', $product->id)->get();
        $priceCategories = PriceCategory::all();

        if(auth()->user()->hasRole('owner')){
            return view('menu.edit', compact('product', 'categories', 'productPrice', 'priceCategories'));
        }
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $validateddata = $request->validate([
            'name' => 'required|min:3',
            'modal' => 'required|regex:/([0-9]+[.,]*)+/',
            'price' => 'required|regex:/([0-9]+[.,]*)+/|gte:modal',
            'category' => 'required',
            'picture' => 'image|file|max:3048',
            'description' => 'required'
        ]);


        $validateddata["modal"] = filter_var($request->modal, FILTER_SANITIZE_NUMBER_INT);
        $validateddata["price"] = filter_var($request->price, FILTER_SANITIZE_NUMBER_INT);

        if ($request->file('picture')) {
            Storage::delete($menu->picture);
            $validateddata['picture'] = $request->file('picture')->store('menu');
        }

        Menu::where('id', $menu->id)
             ->update($validateddata);

        $activity = [
            'user_id' => Auth::id(),
            'action' => 'edited a menu '.strtolower($menu->name)
        ];
        ActivityLog::create($activity);

        return redirect('/menu')->with('success', 'menu has been updated !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        Storage::delete($menu->picture);
        $menu->destroy($menu->id);
        $activity = [
            'user_id' => Auth::id(),
            'action' => 'deleted a menu '.strtolower($menu->name)
        ];
        ActivityLog::create($activity);
        return redirect('/menu');
    }

}

