<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;

class StripeController extends Controller
{
    public function checkout(PurchaseRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

         // SOLD チェック
        if ($item->status === 'sold') {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', 'この商品はすでに購入済みです。');
        }
        
        $paymentMethod = $request->validated()['payment_method'];

        Stripe::setApiKey(config('services.stripe.secret'));

        $postalCode = $request->validated()['postal_code'];
        $address    = $request->validated()['address'];
        $building   = $request->building ?? null;

        // Purchase を作成して status を pending に
        $purchase = Purchase::create([
            'user_id'     => $user->id,
            'item_id'     => $item->id,
            'postal_code' => $postalCode,
            'address'     => $address,
            'building'    => $building,
            'status'      => 'pending',
        ]);

        // Stripe Checkout セッション作成
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
            'metadata' => [
                'purchase_id' => (string) $purchase->id,
                'item_id'     => (string) $item->id,
                'user_id'     => (string) $user->id,
            ],
        ]);

        // stripe_session_id を保存
        $purchase->update([
            'stripe_session_id' => $session->id
        ]);

        // セッションに購入IDを保存（必要であれば）
        session(['purchase_id' => $purchase->id]);

        // Stripe Checkout ページへリダイレクト
        return redirect($session->url);
    }

    public function success()
    {
        return redirect()->route('mypage')
            ->with('message', '購入処理中です。支払い完了後に商品が確保されます。');
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('error', '決済がキャンセルされました。');
    }
}
