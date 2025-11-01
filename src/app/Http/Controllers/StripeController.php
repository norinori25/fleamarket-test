<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function checkout(PurchaseRequest $request)
    {
        $item = Item::findOrFail($request->item_id);
        $paymentMethod = $request->input('payment_method');

        // 🔐 購入データをセッションに保存
        session([
            'purchase' => [
                'item_id' => $item->id,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => [$paymentMethod],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                        'images' => [asset('storage/product_img/' . $item->image_url)],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url'  => route('stripe.cancel', ['item_id' => $item->id]),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        $user = Auth::user();
        $purchase = session('purchase');

        if ($user && $purchase) {

            // 🧾 購入履歴登録
            $user->purchases()->create([
                'item_id' => $purchase['item_id'],
                'quantity' => 1,
                'postal_code' => $purchase['postal_code'],
                'address' => $purchase['address'],
                'building' => $purchase['building'],
                'status' => 'paid',
            ]);

            // 🏷️ 商品を売却状態へ
            Item::where('id', $purchase['item_id'])->update(['status' => 'sold']);

            session()->forget('purchase');
        }

        return redirect()->route('mypage')
            ->with('message', '購入が完了しました！');
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('error', '決済がキャンセルされました。');
    }
}
