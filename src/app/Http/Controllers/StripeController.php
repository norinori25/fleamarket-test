<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;

class StripeController extends Controller
{
    // カード支払い
    public function cardPayment($id)
    {
        $product = Product::findOrFail($id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('products.index'),
            'cancel_url' => route('products.index'),
        ]);

        return redirect($session->url);
    }

    // 🟡 コンビニ支払い
    public function conveniencePayment($id)
    {
        $product = Product::findOrFail($id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['konbini'], // Stripeが対応してる「コンビニ支払い」
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('products.index'),
            'cancel_url' => route('products.index'),
        ]);

        return redirect($session->url);
    }
}
