<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * 新規登録処理 + 認証メール送信
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->create($request->validated());

        event(new Registered($user)); // ✅ 認証メール送信

        return $this->registered($request, $user)
            ?: redirect()->route('verification.notice'); // ✅ 誘導画面へ
    }

    /**
     * 登録後に認証ページへリダイレクト
     */
    protected function registered($request, $user)
    {
        return redirect()->route('verification.notice'); // ✅ 認証に誘導
    }
}
