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

        // メール認証ビューの設定
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        app()->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return Redirect::route('verification.notice');
                }
            };
        });

        // LoginResponse: ログイン成功時に「未認証なら強制ログアウトして認証誘導ページへ」
        app()->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = $request->user();

                    if ($user && ! $user->hasVerifiedEmail()) {
                        // 未認証ならログアウトし、誘導ページへ
                        auth()->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        // フラッシュメッセージを付与
                        return Redirect::route('verification.notice')
                            ->withErrors(['email' => 'メール認証が必要です。認証メールをご確認ください。']);
                    }

                    // デフォルトのリダイレクト（任意で変更）
                    return Redirect::intended(config('fortify.home', '/mypage'));
                }
            };
        });

        Fortify::authenticateUsing(function (Request $request) {
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

            if (!Auth::attempt($credentials,    $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません'],
                ]);
            }

            $request->session()->regenerate();

            return auth()->user();
        });

        // ビューの設定
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
    }

    public function register()
    {
    //
    }

}
