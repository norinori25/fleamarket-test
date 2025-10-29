<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $item = Item::findOrFail($request->item_id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentMethod = $request->input('payment_method', 'card');

        $unitAmount = $item->price;

        $baseUrl = env('NGROK_URL');

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
        'success_url' => env('NGROK_URL') . '/purchase/success',
        'cancel_url'  => env('NGROK_URL') . '/purchase/cancel',
    ]);


        return redirect($session->url);
    }
}
