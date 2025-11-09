<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemDetailListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームで必要情報が正しく保存される()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $uploadedFile = UploadedFile::fake()->image('sample.jpg');
        $categories = Category::factory(1)->create(); // カテゴリーも作る

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'テスト商品',
            'description' => 'テスト用商品説明',
            'brand_name' => 'テストブランド',
            'condition' => '良好',
            'price' => 5000,
            'image' => $uploadedFile,
            'category_ids' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'テスト用商品説明',
            'brand_name' => 'テストブランド',
            'condition' => '良好',
            'price' => 5000
        ]);

        Storage::disk('public')->assertExists('item_images/' . $uploadedFile->hashName());
    }

    /** @test */
    public function 商品詳細ページに情報が正しく表示される()
    {
        $user = User::factory()->create();
        $categories = Category::factory(2)->create();

        $item = Item::factory()->create([
            'name' => '詳細表示用商品',
            'description' => '商品説明',
            'brand_name' => 'ブランドA',
            'price' => 3000,
            'condition' => '良好',
            'status' => 'on_sale',
            'image_url' => '/storage/item_images/test.png',
        ]);

        $item->categories()->sync($categories->pluck('id'));

        $commentUser = User::factory()->create(['name' => 'コメントユーザー']);
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'テストコメント',
        ]);

        $response = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);

        // テキストだけを確認（HTMLタグの影響を避ける）
        $response->assertSeeText('詳細表示用商品');
        $response->assertSeeText('商品説明');
        $response->assertSeeText('ブランドA');
        $response->assertSeeText('¥3,000'); // 税込表記は無視してもOK
        $response->assertSeeText('良好');

        foreach ($categories as $category) {
            $response->assertSeeText($category->name);
        }

        $response->assertSeeText('コメントユーザー');
        $response->assertSeeText('テストコメント');
        $response->assertSeeText('0'); // favorites_count / comments_count

        // 購入ボタン
        $response->assertSee('購入手続きへ');

        // sold商品テスト
        $soldItem = Item::factory()->create([
            'status' => 'sold',
            'name' => 'SOLD',
            'image_url' => '/storage/item_images/sold.png',
        ]);

        $soldResponse = $this->actingAs($user)->get(route('items.show', ['item_id' => $soldItem->id]));
        $soldResponse->assertDontSee('購入手続きへ');
        $soldResponse->assertSeeText('SOLD');

        // ゲストテスト
        $guestResponse = $this->get(route('items.show', ['item_id' => $item->id]));
        $guestResponse->assertSee('購入手続きへ'); // ゲストはログインボタンが表示される
    }

    /** @test */
    public function 商品詳細ページに複数カテゴリが表示される()
    {
        $item = Item::factory()->create(['status' => 'on_sale']);
        $categories = Category::factory(2)->create();
        $item->categories()->sync($categories->pluck('id'));

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    /** @test */
    public function sold商品には購入ボタンが表示されない()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'sold',
            'name' => 'SOLD',
            'image_url' => '/storage/item_images/sold.png',
        ]);

        $response = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertDontSee('購入手続きへ');
        $response->assertSee('SOLD');
    }

    /** @test */
    public function ログイン時のみコメントできる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '販売中商品',
            'status' => 'on_sale',
        ]);

        // 未ログイン
        $guestResponse = $this->get(route('items.show', ['item_id' => $item->id]));
        $guestResponse->assertStatus(200);
        // 未ログイン時はコメントフォームのactionがloginになっている
        $guestResponse->assertSee('action="' . route('login') . '"', false);

        // ログイン後
        $authResponse = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));
        $authResponse->assertStatus(200);
        // ログイン時はコメントフォームのactionがcomments.storeになっている
        $authResponse->assertSee('action="' . route('comments.store', ['item_id' => $item->id]) . '"', false);
    }

    /** @test */
public function ログイン済みユーザーはコメントを送信できる()
{
    $user = \App\Models\User::factory()->create();
    $item = \App\Models\Item::factory()->create();

    $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
        'content' => 'テストコメント',
    ]);

    // コメントがDBに保存されているか確認
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'content' => 'テストコメント',
    ]);

    // コメント数が増えている
    $this->assertEquals(1, $item->comments()->count());

    // リダイレクト先を確認（例：商品詳細ページなど）
    $response->assertRedirect();
}

/** @test */
public function コメントが空の場合バリデーションエラーになる()
{
    $user = \App\Models\User::factory()->create();
    $item = \App\Models\Item::factory()->create();

    $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
        'content' => '',
    ]);

    $response->assertSessionHasErrors(['content']);
}

/** @test */
public function コメントが255字以上の場合バリデーションエラーになる()
{
    $user = \App\Models\User::factory()->create();
    $item = \App\Models\Item::factory()->create();

    $longComment = str_repeat('あ', 256);

    $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
        'content' => $longComment,
    ]);

    $response->assertSessionHasErrors(['content']);
}

}
