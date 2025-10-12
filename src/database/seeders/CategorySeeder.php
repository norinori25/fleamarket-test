<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product; // ← 必須

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション', '家電', 'インテリア', 'レディース',
            'メンズ', 'コスメ', '本', 'ゲーム', 'スポーツ',
            'キッチン', 'ハンドメイド', 'アクセサリー',
            'おもちゃ', 'ベビー・キッズ'
        ];

        // カテゴリーを作成・重複回避
        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }

        // --- 統合処理 ---
        // 統合カテゴリー取得（なければ作成）
        $babyKids = Category::firstOrCreate(['name' => 'ベビー・キッズ']);

        // 旧カテゴリーIDを取得（削除前に）
        $oldCategories = Category::whereIn('name', ['ベビー', 'キッズ'])->pluck('id');

        // products の category_id を統合カテゴリーに更新
        Product::whereIn('category_id', $oldCategories)->update([
            'category_id' => $babyKids->id
        ]);

        // 旧カテゴリーを削除
        Category::whereIn('name', ['ベビー', 'キッズ'])->delete();
    }
}
