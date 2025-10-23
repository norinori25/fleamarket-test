<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // セッションに一時保存された住所があれば優先
        $shippingAddress = session('shipping_address', [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        return view('purchase.show', compact('item', 'shippingAddress'));
    }

    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // 現在の住所を初期値に
        $shippingAddress = session('shipping_address', [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        return view('purchase.address', compact('item', 'shippingAddress'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        $validated = $request->validate([
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        // セッションに保存
        session(['shipping_address' => $validated]);

        return redirect()->route('purchase.show', ['item_id' => $item_id])
                         ->with('status', '配送先を更新しました。');
    }

    // 支払い方法選択画面
    public function payment($item_id)
    {
        $item = item::findOrFail($item_id);
        return view('purchase.payment', compact('item'));
    }

    // 購入処理（最終）
    public function store(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'card') {
        // Stripeのカード支払いへ
            return redirect()->route('stripe.card', ['item_id' => $item->id]);
        } elseif ($paymentMethod === 'convenience') {
        // Stripeのコンビニ支払いへ
            return redirect()->route('stripe.convenience', ['item_id' => $item->id]);
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
