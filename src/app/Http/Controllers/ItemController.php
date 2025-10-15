<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function show($item_id)
    {
        // 今はシンプルに商品データだけ取得
        $product = Product::findOrFail($item_id);

        return view('products.show', compact('product'));
    }
}
