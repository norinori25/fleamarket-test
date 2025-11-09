<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class TestItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
        ]);

        $categories = Category::factory(5)->create();

        // 販売中商品
        $item1 = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '販売中商品',
            'status' => 'on_sale',
        ]);
        $item1->categories()->sync($categories->pluck('id'));

        // SOLD商品
        $item2 = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '購入済み商品',
            'status' => 'sold',
        ]);
        $item2->categories()->sync($categories->pluck('id'));
    }
}
