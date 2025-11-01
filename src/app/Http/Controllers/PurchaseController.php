<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

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

    public function updateAddress(AddressRequest $request, $item_id)
    {
        $validated = $request->validated();

        // セッションに保存
        session(['shipping_address' => $validated]);

        return redirect()->route('purchase.show', ['item_id' => $item_id])->with('status', '配送先を更新しました。');
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $shipping = session('shipping_address');
        if (!$shipping) {
            return back()->with('error', '配送先情報がありません。');
        }

        // ✅ 仮購入レコード作成 (pending)
        $purchase = Purchase::create([
            'user_id'      => $user->id,
            'item_id'      => $item->id,
            'postal_code'  => $shipping['postal_code'],
            'address'      => $shipping['address'],
            'building'     => $shipping['building'] ?? null,
            'status'       => 'pending',
        ]);

        // ✅ purchase_id をセッションに保存して Stripe へ
        session(['purchase_id' => $purchase->id]);

        return redirect()->route('checkout')->with('payment_method', $request->payment_method);
    }
}
