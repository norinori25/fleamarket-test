<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\StripeController;

class StripeTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_flow_including_address_payment_and_profile()
    {
        // 1. ユーザー作成＆ログイン
        $user = User::factory()->create([
            'postal_code' => '111-1111',
            'address'     => '東京都テスト区',
            'building'    => 'テストマンション101',
        ]);
        $this->actingAs($user);

        // 2. 商品作成
        $item = Item::factory()->create([
            'status' => 'on_sale',
        ]);

        // 3. Stripe モック用の固定 URL
        $mockUrl = 'https://stripe.test/checkout/session/12345';

        $this->app->instance(StripeController::class, new class($mockUrl) extends StripeController {
            private $mockUrl;
            public function __construct($url) { $this->mockUrl = $url; }

            public function checkout(\App\Http\Requests\PurchaseRequest $request, $item_id)
            {
                $user = auth()->user();
                $item = \App\Models\Item::find($item_id);

                // Purchase を作成
                $purchase = \App\Models\Purchase::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'postal_code' => $request->postal_code,
                    'address' => $request->address,
                    'building' => $request->building,
                    'status' => 'pending',
                ]);

                return redirect($this->mockUrl);
            }
        });

        // 4. 配送先を変更（セッションでも OK）
        $response = $this->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '222-2222',
            'address'     => '大阪府テスト市',
            'building'    => 'テストビル202',
        ]);
        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));
        $this->assertEquals(session('shipping_address')['postal_code'], '222-2222');

        // 5. 購入リクエスト送信（支払い方法はカード）
        $response2 = $this->post(route('checkout', ['item_id' => $item->id]), [
            'payment_method' => 'card',
            'postal_code' => session('shipping_address')['postal_code'],
            'address'     => session('shipping_address')['address'],
            'building'    => session('shipping_address')['building'],
        ]);

        // Stripe のモック URL にリダイレクト
        $response2->assertRedirect($mockUrl);

        // 6. DB に Purchase が作成されている
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '222-2222',
            'status' => 'pending',
        ]);

        $purchase = Purchase::first();

        // 7. 購入完了後にステータス更新
        $purchase->update(['status' => 'paid']);
        $item->update(['status' => 'sold']);

        $this->assertEquals('paid', $purchase->fresh()->status);
        $this->assertEquals('sold', $item->fresh()->status);

        // 8. プロフィール購入一覧に商品が表示されている
        $response3 = $this->get(route('mypage') . '?page=buy');
        $response3->assertSee($item->name);
        $response3->assertSee('SOLD');

        // 9. 配送先も表示されている
        $response3->assertSee($purchase->postal_code);
        $response3->assertSee($purchase->address);
        $response3->assertSee($purchase->building);
    }
}
