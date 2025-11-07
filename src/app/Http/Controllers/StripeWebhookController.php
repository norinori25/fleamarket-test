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

            \Log::info('Webhook triggered for Session ID: ' . $session->id);

            // Stripe Checkout で送った metadata から purchase_id を取得
            $purchaseId = $session->metadata->purchase_id ?? null;

            if ($purchaseId) {
                $purchase = Purchase::find($purchaseId);

                if ($purchase) {
                    // Purchase のステータス更新
                    $purchase->update(['status' => 'paid']);

                    // Item のステータス更新
                    if ($purchase->item) {
                        $purchase->item->update(['status' => 'sold']);
                        \Log::info('Purchase and Item updated successfully. Purchase ID: ' . $purchase->id);
                    } else {
                        \Log::warning('Item not found for Purchase ID: ' . $purchase->id);
                    }
                } else {
                    \Log::warning('Purchase not found for ID: ' . $purchaseId);
                }
            } else {
                \Log::warning('purchase_id not found in session metadata. Session ID: ' . $session->id);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
