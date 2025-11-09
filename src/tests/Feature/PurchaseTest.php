<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 支払い方法と配送先を選択できる()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都〇〇',
            'building' => 'マンション101',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        // 配送先変更
        $response = $this->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '987-6543',
            'address' => '大阪府△△',
            'building' => 'ビル202',
        ]);

        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));
        $this->assertEquals(session('shipping_address')['postal_code'], '987-6543');

        // 支払い方法を選択して購入画面に進む
        $response2 = $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'card',
            'postal_code' => session('shipping_address')['postal_code'],
            'address' => session('shipping_address')['address'],
            'building' => session('shipping_address')['building'],
        ]);

        $response2->assertRedirect(route('checkout', ['item_id' => $item->id]));
        $this->assertEquals(session('purchase_payment_method'), 'card');
    }
}
