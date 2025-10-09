@extends('layouts.app')

@section('content')
<div class="profile-edit">
    <h1>プロフィール編集</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <label>名前</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}">
        
        <label>自己紹介</label>
        <textarea name="bio">{{ old('bio', $user->bio) }}</textarea>
        
        <label>住所</label>
        <input type="text" name="address" value="{{ old('address', $user->address) }}">

        <button type="submit">更新する</button>
    </form>

    <a href="{{ route('mypage.index') }}">戻る</a>
</div>
@endsection
