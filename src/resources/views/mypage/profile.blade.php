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

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="image-upload">
            <div class="image-wrapper">
                <img id="profile-img"
                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/default.png') }}"
                    class="profile-img">
            </div>
            <input type="file" name="profile_image" id="profile_image" style="display:none;">
            <button type="button" class="select-btn" onclick="document.getElementById('profile_image').click()">画像を選択する</button>
            @error('profile_image')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
            @error('postal_code')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
            @error('address')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="btn">更新する</button>
    </form>
</div>

<script>
const input = document.getElementById('profile_image');
const preview = document.getElementById('profile-img');

input.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
