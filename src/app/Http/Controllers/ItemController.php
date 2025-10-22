<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;


class ItemController extends Controller
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
            $items = $query->latest()->get();
        } else {
            $query = Item::query();

            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")->orWhere('brand_name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%");
                });
            }

            $items = $query->latest()->get();
        }

        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $items = Item::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->latest()
            ->get();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        $user = auth()->user(); // ← ログイン中のユーザーを取得
        return view('items.create', compact('categories', 'user'));
    }

    public function store(Request $request)
    {
        $categoryIds = json_decode($request->category_ids, true); // ← ここで変換

        $request->merge(['category_ids' => $categoryIds]); // ← 配列に差し替え
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'brand_name' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category_ids' => 'required|array', // ← 配列として受け取る
            'category_ids.*' => 'integer|exists:categories,id',
            'condition' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('item_images', 'public');
        }

        $item = Item::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'brand_name' => $request->brand_name,
            'image_url' => '/storage/' . $path,
            'condition' => $request->condition ?? '良好',
            'status' => 'on_sale',
        ]);

        // 商品とカテゴリーの多対多登録
        $item->categories()->sync($request->category_ids);

        return redirect()->route('mypage')->with('success', '商品を出品しました！');
    }


    public function show($item_id)
    {
        $item = Item::withCount(['favorites', 'comments'])
            ->with(['comments.user', 'categories'])
            ->findOrFail($item_id);

        return view('items.show', compact('item'));
    }
}
