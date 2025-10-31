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
        // 購入商品IDをセッションに保存
        session(['purchase_item_id' => $request->item_id]);

        $item = Item::findOrFail($request->item_id);
        $paymentMethod = $request->input('payment_method');

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

            // ✅ 成功URL：StripeController@success
            'success_url' => route('stripe.success'),

            // ✅ キャンセルURL：StripeController@cancel
            'cancel_url'  => route('stripe.cancel', ['item_id' => $item->id]),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        $user = Auth::user();
        $itemId = session('purchase_item_id');

        if ($user && $itemId) {
            // ✅ 購入履歴保存（中間テーブル）
            $user->purchasedItems()->attach($itemId);

            // ✅ 商品ステータス更新
            Item::where('id', $itemId)->update(['status' => 'sold']);

            // ✅ セッション削除
            session()->forget('purchase_item_id');
        }

        return redirect()->route('mypage')
            ->with('message', '購入が完了しました！');
    }

    public function cancel($item_id)
    {
        // キャンセル時は購入画面へ戻す
        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('error', '決済がキャンセルされました。');
    }
}
