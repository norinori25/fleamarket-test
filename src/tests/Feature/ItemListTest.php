<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\TestItemSeeder::class);
    }

    /** @test */
    public function 商品一覧で全商品を取得できる()
    {
        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('自分の商品');
        $response->assertSee('購入済み商品');
        $response->assertSee('販売中商品');
    }

    /** @test */
    public function 購入済み商品は_sold_と表示される()
    {
        $response = $this->get(route('items.index'));

        $response->assertSee('SOLD');
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $user = User::where('email', 'testuser@example.com')->first();
        $this->actingAs($user);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
    }
}
