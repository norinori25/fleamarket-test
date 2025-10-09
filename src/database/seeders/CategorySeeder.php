<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション',
            'メンズ',
            'レディース',
            'コスメ',
            'アクセサリー',
            '本',
            'ゲーム',
            'スポーツ',
            '家電',
            'インテリア',
            'キッチン',
            'ハンドメイド',
            'おもちゃ',
            'ベビー',
            'キッズ',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
