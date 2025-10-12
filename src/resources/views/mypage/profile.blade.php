@extends('layouts.app')

@section('title', 'プロフィール設定画面')

@push('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="profile-edit-container">
    <h1>プロフィール設定</h1>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="image-upload">
            <div>
                <img src="sample.jpg" class="profile-img">

                @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" width="120">
                @endif
            </div>
            <div>
                <button type="button" class="select-btn">画像を選択する</button>
            </div>
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="btn">更新する</button>
    </form>
</div>
@endsection
