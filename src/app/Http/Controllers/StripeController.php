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
    public function checkout(PurchaseRequest $request)
    {
        $user = Auth::user();
        $item = Item::findOrFail($request->item_id);
        $paymentMethod = $request->input('payment_method');

        Stripe::setApiKey(config('services.stripe.secret'));

        $postalCode = $request->postal_code;
        $address    = $request->address;
        $building   = $request->building ?? null;



        $purchase = Purchase::create([
            'user_id'     => $user->id,
            'item_id'     => $item->id,
            'postal_code' => $postalCode,
            'address'     => $address,
            'building'    => $building,
            'status'      => 'pending',
        ]);


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

        $purchase->update([
        'stripe_session_id' => $session->id
        ]);

        session(['purchase_id' => $purchase->id]);

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
