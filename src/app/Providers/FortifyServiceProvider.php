<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ユーザー登録処理は CreateNewUser クラスに任せる
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録後にプロフィールページへリダイレクト
        app()->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                public function toResponse($request)
                {
                    return Redirect::route('profile.edit');
                }
            };
        });

        // ログイン処理
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        // ログイン画面を表示するBladeを指定
        Fortify::loginView(fn() => view('auth.login'));

        // 新規登録画面を表示するBladeを指定
        Fortify::registerView(fn() => view('auth.register'));
    }
}
