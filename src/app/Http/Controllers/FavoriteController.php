<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($itemId)
    {
        $user = Auth::user();

        if ($user->favorites()->where('item_id', $itemId)->exists()) {
            $user->favorites()->detach($itemId); // 削除
        } else {
            $user->favorites()->attach($itemId); // 登録
        }

        $item = Item::findOrFail($itemId);

        return redirect()->route('items.show', ['item_id' => $item->id]);
    }

    public function index()
    {
        $favorites = Auth::user()->favorites()->latest()->get();
        return view('favorites.index', compact('favorites'));
    }
}

