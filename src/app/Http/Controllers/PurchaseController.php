<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 商品購入画面（メイン画面）
    public function show($item_id)
    {
        $product = Product::findOrFail($item_id);
        $user = Auth::user();
        return view('purchase.show', compact('product', 'user'));
    }

    // 支払い方法選択画面
    public function payment($item_id)
    {
        $product = Product::findOrFail($item_id);
        return view('purchase.payment', compact('product'));
    }

    // 住所変更画面
    public function editAddress($item_id)
    {
        $product = Product::findOrFail($item_id);
        $user = Auth::user();
        return view('purchase.address', compact('product', 'user'));
    }

    // 住所更新処理
    public function updateAddress(Request $request, $item_id)
    {
        $request->validate(['address' => 'required|max:255']);
        $user = Auth::user();
        $user->update(['address' => $request->address]);
        return redirect()->route('purchase.show', $item_id)
                         ->with('success', '住所を更新しました！');
    }

    // 購入処理（最終）
    public function store(Request $request, $item_id)
    {
        $product = Product::findOrFail($item_id);
        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'card') {
        // Stripeのカード支払いへ
            return redirect()->route('stripe.card', ['item_id' => $product->id]);
        } elseif ($paymentMethod === 'convenience') {
        // Stripeのコンビニ支払いへ
            return redirect()->route('stripe.convenience', ['item_id' => $product->id]);
        }

        return back()->with('error', '支払い方法を選択してください');
    }

    // 決済成功時
    public function success()
    {
        return view('purchase.success');
    }

    // 決済キャンセル時
    public function cancel()
    {
        return view('purchase.cancel');
    }

}
