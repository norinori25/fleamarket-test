<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($productId)
    {
        $user = Auth::user();

        if ($user->favorites()->where('product_id', $productId)->exists()) {
            $user->favorites()->detach($productId); // 削除
        } else {
            $user->favorites()->attach($productId); // 登録
        }

        $product = Product::findOrFail($productId);

        return redirect()->route('products.show', ['item_id' => $product->id]);
    }

    public function index()
    {
        $favorites = Auth::user()->favorites()->latest()->get();
        return view('favorites.index', compact('favorites'));
    }
}

