<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');

        if ($tab === 'mylist' && auth()->check()) {
            $favorites = auth()->user()->favorites()->latest()->with('product')->get();
            $products = $favorites->pluck('product')->filter();
        } else {
        $products = Product::latest()->get();
        }

        return view('products.index', compact('products', 'tab'));
    }


    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = Product::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'brand_name' => 'nullable|string|max:255',
            'image_url' => 'required|url',
            'category_id' => 'required|integer|exists:categories,id',
            'condition' => 'nullable|string|max:255',
        ]);

        \App\Models\Product::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'brand_name' => $request->brand_name,
            'image_url' => $request->image_url,
            'condition' => $request->condition ?? '良好',
            'status' => 'on_sale',
        ]);

        return redirect()->route('home')->with('success', '商品を出品しました！');
    }

}
