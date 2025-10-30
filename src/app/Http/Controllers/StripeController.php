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
                        'images' => [$item->image_url],
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
}