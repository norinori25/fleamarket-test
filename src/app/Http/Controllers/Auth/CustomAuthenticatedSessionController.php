<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthenticatedSessionController extends Controller
{
    /**
     * ログアウト処理
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        // セッションを完全に破棄
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログアウト後のリダイレクト先（トップページに飛ばす）
        return redirect('/');
    }
}
