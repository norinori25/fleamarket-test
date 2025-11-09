<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

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

        session(['shipping_address' => $shippingAddress]);

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
        $validated = $request->validated();
        
        $shipping = session('shipping_address');
        if (!$shipping) {
            return back()->with('error', '配送先情報がありません。');
        }

        session(['purchase_payment_method' => $request->payment_method]);

        return redirect()->route('checkout', ['item_id' => $item_id]);
    }
}
