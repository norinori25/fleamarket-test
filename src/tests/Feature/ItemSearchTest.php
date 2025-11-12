<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品名で部分一致検索ができる()
    {
        $user = User::factory()->create();

        Item::factory()->create(['name' => '赤いバッグ']);
        Item::factory()->create(['name' => '青いバッグ']);
        Item::factory()->create(['name' => '靴']);

        $response = $this->actingAs($user)->get(route('home', ['keyword' => 'バッグ']));

        $response->assertStatus(200);
        $response->assertSee('赤いバッグ');
        $response->assertSee('青いバッグ');
        $response->assertDontSee('靴');
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();

        // 商品を3つ作成
        $item1 = Item::factory()->create(['name' => '赤いバッグ']);
        $item2 = Item::factory()->create(['name' => '青いバッグ']);
        $item3 = Item::factory()->create(['name' => '靴']);

        // 赤いバッグと青いバッグをお気に入り登録
        $user->favorites()->attach([$item1->id, $item2->id]);

        // 🔍 検索キーワード「バッグ」でマイリストを表示
        $response = $this->actingAs($user)->get(route('home', [
            'tab' => 'mylist',
            'keyword' => 'バッグ'
        ]));

        $response->assertStatus(200);

        // ✅ マイリストにバッグ関連の商品が表示される
        $response->assertSee('赤いバッグ');
        $response->assertSee('青いバッグ');
        $response->assertDontSee('靴');

        // ✅ 検索キーワードがフォームに保持されている（HTMLエスケープ無視）
        $response->assertSee('value="バッグ"', false);
    }
}
