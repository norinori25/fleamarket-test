<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist' && auth()->check()) {
            $query = auth()->user()->favorites();
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")->orWhere('brand_name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%");
                });
            }
            $products = $query->latest()->get();
        } else {
            $query = Product::query();

            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")->orWhere('brand_name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%");
                });
            }

            $products = $query->latest()->get();
        }

        return view('products.index', compact('products', 'tab', 'keyword'));
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
        $categories = Category::all();
        $user = auth()->user(); // ← ログイン中のユーザーを取得
        return view('products.create', compact('categories', 'user'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'brand_name' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'condition' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_images', 'public');
        }

        Product::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'brand_name' => $request->brand_name,
            'image_url' => '/storage/' . $path, // URLとして保存
            'condition' => $request->condition ?? '良好',
            'status' => 'on_sale',
        ]);

        return redirect()->route('mypage')->with('success', '商品を出品しました！');
    }

}
