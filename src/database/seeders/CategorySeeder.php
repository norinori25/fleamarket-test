<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション', '家電', 'インテリア', 'レディース', 'メンズ',
            'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン',
            'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ',
        ];

        foreach ($categories as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
