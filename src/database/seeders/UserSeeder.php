<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ダミーユーザーを5件作成
        User::factory(5)->create();
    }
}
