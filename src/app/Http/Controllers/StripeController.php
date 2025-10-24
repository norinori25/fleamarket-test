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

// 日本円なのでそのまま整数でOK
$unitAmount = $item->price;

$session = Session::create([
    'payment_method_types' => [$paymentMethod], // 'card' か 'konbini'
    'line_items' => [[
        'price_data' => [
            'currency' => 'jpy',
            'unit_amount' => $unitAmount,
            'product_data' => [
                'name' => $item->name,
                'images' => [$item->image_url],
            ],
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => route('items.index'),
    'cancel_url' => route('items.show', $item->id),
]);

return redirect($session->url);

    }

}
