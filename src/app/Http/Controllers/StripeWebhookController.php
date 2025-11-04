<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            \Log::error('Invalid Stripe signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Webhook で受け取った session ID をログ
            \Log::info('Webhook Session ID: ' . $session->id);

            // 本来は stripe_session_id で検索するけど、テスト用に最新 Purchase を使う
            $purchase = Purchase::where('stripe_session_id', $session->id)->first();
            if (!$purchase) {
                $purchase = Purchase::latest()->first();
            }

            if ($purchase) {
                $purchase->update(['status' => 'paid']);
                $purchase->item->update(['status' => 'sold']);
                \Log::info('Purchase and Item updated: ' . $purchase->id);
            } else {
                \Log::warning('Purchase not found for session: ' . $session->id);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
