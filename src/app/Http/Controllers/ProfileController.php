<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bio'  => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('name', 'bio', 'address'));

        return redirect()->route('mypage.index')->with('status', 'プロフィールを更新しました！');
    }
}
