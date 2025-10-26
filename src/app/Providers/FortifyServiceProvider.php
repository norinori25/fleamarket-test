<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        app()->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return Redirect::route('profile.edit');
                }
            };
        });

    Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
    $rules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ];

    $messages = [
        'email.required' => 'メールアドレスを入力してください',
        'email.email' => 'メールアドレスはメール形式で入力してください',
        'password.required' => 'パスワードを入力してください',
    ];

    $request->validate($rules, $messages);

    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }

    $request->session()->regenerate();

    return Auth::user();
});



        // ビューの設定
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
    }
}
