<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = \App\Models\Item::class;

    public function definition(): array
    {
        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(500, 10000),
            'brand_name' => $this->faker->optional()->word(),
            'image_url' => '/storage/item_images/default.jpg',
            'condition' => $this->faker->randomElement($conditions),
            'status' => 'on_sale',
        ];
    }
}
