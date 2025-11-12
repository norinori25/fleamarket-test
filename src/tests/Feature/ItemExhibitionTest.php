<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームで必須情報が正しく保存される()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $uploadedFile = UploadedFile::fake()->image('item.jpg');
        $categories = Category::factory(2)->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => '完全テスト商品',
            'description' => 'テスト用の商品説明です',
            'brand_name' => 'ブランドX',
            'price' => 10000,
            'condition' => '新品',
            'image' => $uploadedFile,
            'category_ids' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('mypage'));

        // DBに登録されているか確認
        $this->assertDatabaseHas('items', [
            'name' => '完全テスト商品',
            'description' => 'テスト用の商品説明です',
            'brand_name' => 'ブランドX',
            'price' => 10000,
            'condition' => '新品',
            'status' => 'on_sale',
            'user_id' => $user->id,
        ]);

        // 画像がStorageに保存されているか確認
        Storage::disk('public')->assertExists('item_images/' . $uploadedFile->hashName());

        // カテゴリの多対多も確認
        $item = Item::first();
        $this->assertCount(2, $item->categories);
    }

    /** @test */
    public function 商品詳細ページに情報が正しく表示される()
    {
        $user = User::factory()->create();
        $categories = Category::factory(2)->create();

        $item = Item::factory()->create([
            'name' => '詳細テスト商品',
            'description' => '詳細用の商品説明',
            'brand_name' => 'ブランドY',
            'price' => 5000,
            'condition' => '良好',
            'status' => 'on_sale',
            'image_url' => '/storage/item_images/test.png',
            'user_id' => $user->id,
        ]);

        $item->categories()->sync($categories->pluck('id'));

        $response = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSeeText('詳細テスト商品');
        $response->assertSeeText('詳細用の商品説明');
        $response->assertSeeText('ブランドY');
        $response->assertSeeText('¥5,000');
        $response->assertSeeText('良好');

        foreach ($categories as $category) {
            $response->assertSeeText($category->name);
        }

        $response->assertSee($item->image_url);
        $response->assertSee('購入手続きへ');
    }

    /** @test */
    public function sold商品には購入ボタンが表示されない()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'SOLDテスト商品',
            'status' => 'sold',
        ]);

        $response = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));
        $response->assertDontSee('購入手続きへ');
        $response->assertSeeText('SOLDテスト商品');
    }

    /** @test */
    public function バリデーションエラーの確認()
    {
        $user = User::factory()->create();

        // 空データ送信
        $response = $this->actingAs($user)->post(route('items.store'), []);
        $response->assertSessionHasErrors([
            'name', 'description', 'price', 'image', 'category_ids', 'condition'
        ]);

        // 価格に文字列
        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => '商品',
            'description' => '説明',
            'price' => '文字列',
            'image' => UploadedFile::fake()->image('item.jpg'),
            'category_ids' => [1],
            'condition' => '新品'
        ]);
        $response->assertSessionHasErrors(['price']);

        // カテゴリID不正
        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => '商品',
            'description' => '説明',
            'price' => 1000,
            'image' => UploadedFile::fake()->image('item.jpg'),
            'category_ids' => [999], // 存在しないID
            'condition' => '新品'
        ]);
        $response->assertSessionHasErrors(['category_ids.0']);
    }
}
