<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\TestItemSeeder::class);
    }

    /** @test */
    public function マイリストにお気に入り商品が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);

        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    /** @test */
    public function お気に入り解除後はマイリストに表示されない()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);
        $user->favorites()->detach($item->id);
        $user->load('favorites');

        $response = $this->get(route('home', ['tab' => 'mylist']));
        $response->assertStatus(200);
        $response->assertDontSee($item->name);
    }

    /** @test */
    public function 未ログイン時はマイリストにアクセスできない()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 商品詳細ページでいいねを追加・解除できる_見た目とカウントも確認()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        // 1回目いいね
        $this->post(route('favorites.toggle', ['item' => $item->id]));
        $item->load('favorites');

        $response = $this->get(route('items.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee((string)($item->favorites()->count())); // カウント確認


        // 2回目いいね解除
        $this->post(route('favorites.toggle', ['item' => $item->id]));
        $item->load('favorites');

        $response = $this->get(route('items.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee((string)($item->favorites()->count())); // 0になるはず
    }
}
