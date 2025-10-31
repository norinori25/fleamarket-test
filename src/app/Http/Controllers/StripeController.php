<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;

class StripeController extends Controller
{
    public function checkout(PurchaseRequest $request)
    {
        session(['purchase_item_id' => $request->item_id]);

        $item = Item::findOrFail($request->item_id);
        $paymentMethod = $request->input('payment_method');
        $shippingAddressId = $request->input('shipping_address_id');

        Stripe::setApiKey(config('services.stripe.secret'));

        $unitAmount = $item->price;

        $baseUrl = config('app.url');

        $session = Session::create([
            'payment_method_types' => [$paymentMethod],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int)$unitAmount,
                    'product_data' => [
                        'name' => $item->name,
                        'images' => [asset('storage/product_img/' . $item->image_url)],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl . '/success',
            'cancel_url'  => $baseUrl . '/cancel',
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $user = auth()->user();
        $itemId = session('purchase_item_id');

        if ($user && $itemId) {
            // 購入履歴を保存
            $user->purchasedItems()->attach($itemId);

            // 商品のステータス更新（売り切れ等がある場合）
            Item::where('id', $itemId)->update(['status' => 'sold']);
        }

        return redirect('/mypage')
        ->with('message', '購入が完了しました！');
    }

}