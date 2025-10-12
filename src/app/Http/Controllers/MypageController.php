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
        $user = auth()->user();
        $tab = $request->query('tab', 'listed');

        if ($tab === 'purchased') {
            $products = $user->purchasedProducts()->latest()->get();
        } else {
        $products = $user->products()->latest()->get();
        }

        return view('mypage.index', compact('user', 'products', 'tab'));
    }
}
