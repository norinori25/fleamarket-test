<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $page = $request->query('page', 'sell');

        if ($page === 'sell') {
            // 出品した商品
            $items = $user->items;
        } else {
            // 購入履歴を Purchase 経由で取得
            $purchases = $user->purchases()->with('item')->get();
        }

        return view('mypage.index', compact('user', 'items'));
    }

}
