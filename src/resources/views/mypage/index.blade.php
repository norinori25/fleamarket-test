@extends('layouts.app')

@section('title', 'マイページ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage-header">
    <div class="user-info">
        <img src="{{ $user->profile_image ?? asset('img/default_user.png') }}" alt="プロフィール画像" class="user-icon">
        <h2>{{ $user->name }}</h2>
    </div>

    <a href="{{ route('profile.edit') }}" class="edit-btn">プロフィールを編集</a>
</div>

<div class="mypage-tabs">
    <a href="{{ url('/mypage?tab=listed') }}" class="{{ request('tab') === 'listed' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=purchased') }}" class="{{ request('tab') === 'purchased' ? 'active' : '' }}">購入した商品</a>
</div>

<div class="mypage-products">
    @forelse ($products as $product)
        <div class="product-card">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <p class="product-name">{{ $product->name }}</p>
            <p class="product-price">¥{{ number_format($product->price) }}</p>
        </div>
    @empty
        <p class="no-products">商品がありません。</p>
    @endforelse
</div>
@endsection
