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
            $items = $user->items()->get();
            $purchases = collect();
        } else {
            // 購入履歴
            $purchases = $user->purchases()->with('item')->get();
            $items = collect(); // 空コレクション
        }

        return view('mypage.index', compact('user', 'page', 'items', 'purchases'));
    }
}
