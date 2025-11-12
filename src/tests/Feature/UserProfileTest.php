<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入画面に住所が反映される()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区テスト1-2-3',
            'building' => 'テストビル1F',
        ]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSeeText('123-4567');
        $response->assertSeeText('東京都渋谷区テスト1-2-3');
        $response->assertSeeText('テストビル1F');

        // セッションに保存されているかも確認
        $this->assertEquals(session('shipping_address.postal_code'), '123-4567');
        $this->assertEquals(session('shipping_address.address'), '東京都渋谷区テスト1-2-3');
        $this->assertEquals(session('shipping_address.building'), 'テストビル1F');
    }

    /** @test */
    public function 配送先更新後に購入画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 住所更新
        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市サンプル町1-1',
            'building' => 'サンプルマンション101',
        ]);

        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));

        // 購入画面で反映されるか確認
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertSeeText('987-6543');
        $response->assertSeeText('大阪府大阪市サンプル町1-1');
        $response->assertSeeText('サンプルマンション101');
    }

    public function プロフィールページで正しいユーザー情報が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => '/storage/profile/test.png',
            'postal_code' => '111-2222',
            'address' => '東京都世田谷区テスト1-2-3',
        ]);

        // 出品商品作成
        $items = Item::factory(2)->create(['user_id' => $user->id]);

        // 購入商品作成
        $purchases = Purchase::factory(2)->create([
            'user_id' => $user->id,
            'item_id' => Item::factory()->create()->id,
        ]);

        $response = $this->actingAs($user)->get(route('mypage.index'));

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('/storage/profile/test.png');
        $response->assertSeeText('111-2222');
        $response->assertSeeText('東京都世田谷区テスト1-2-3');

        // 出品商品一覧確認
        foreach ($items as $item) {
            $response->assertSeeText($item->name);
            $response->assertSee($item->image_path);
        }

        // 購入商品一覧確認
        foreach ($purchases as $purchase) {
            $response->assertSeeText($purchase->item->name);
            $response->assertSee($purchase->item->image_path); // 画像表示確認
            $this->assertDatabaseHas('purchases', [
                'id' => $purchase->id,
                'postal_code' => $purchase->postal_code,
                'address' => $purchase->address,
                'building' => $purchase->building,
            ]);
        }
    }

    /** @test */
    public function プロフィール編集画面で過去設定が初期値として表示される()
    {
        $user = User::factory()->create([
            'name' => '旧ユーザー',
            'profile_image' => '/storage/profile/old.png',
            'postal_code' => '111-2222',
            'address' => '東京都世田谷区旧住所1-2-3',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('旧ユーザー');
        $response->assertSee('111-2222');
        $response->assertSee('東京都世田谷区旧住所1-2-3');
        $response->assertSee('/storage/profile/old.png');
    }
}
