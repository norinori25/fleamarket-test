@extends('layouts.app')

@section('title', '送付先住所の変更')

@push('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('search-form')
@include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="register-container">
    <h1>送付先住所を変更</h1>

    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="address">新しい住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" required>
            @error('address')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">保存する</button>

        <div class="login-link">
            <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}">戻る</a>
        </div>
    </form>
</div>
@endsection
