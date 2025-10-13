<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $page = $request->query('page', 'sell');

        if ($page === 'sell') {
            // 出品した商品
            $products = $user->products;
        } else {
            // 購入した商品
            $products = $user->purchasedProducts;
        }

        return view('mypage.index', compact('user', 'products'));
    }

}
