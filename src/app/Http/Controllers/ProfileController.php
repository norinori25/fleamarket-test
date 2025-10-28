<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

   public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 画像アップロード処理
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // 他の項目を更新
        $user->fill($request->only(['name', 'postal_code', 'address', 'building']))->save();

        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
    }

}
