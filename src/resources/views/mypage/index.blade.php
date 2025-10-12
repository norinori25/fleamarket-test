@extends('layouts.app')

@section('title', 'マイページ')

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
<div class="mypage-container">
    <!-- ユーザー情報 -->
    <div class="mypage-header">
        <div class="user-info">
            <img src="{{ $user->profile_image ?? asset('img/default_user.png') }}" alt="プロフィール画像" class="profile-img">
        </div>
        <div class="user-info">
            <p>{{ $user->name }}</p>
        </div>
    </div>
    <div>
        <a href="{{ route('profile.edit') }}" class="edit-btn">プロフィールを編集</a>
    </div>
</div>

    <!-- タブ切替 -->
    <div class="mypage-tabs">
        <a href="{{ url('/mypage?tab=listed') }}" class="{{ request('tab') === 'listed' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('/mypage?tab=purchased') }}" class="{{ request('tab') === 'purchased' ? 'active' : '' }}">購入した商品</a>
    </div>

    <!-- 商品一覧 -->
    <div class="mypage-products">
        @forelse ($products as $product)
            <div class="product-card">
                <img src="{{ $product->image_url ?? asset('img/default_product.png') }}" alt="{{ $product->name }}">
                <p class="product-name">{{ $product->name }}</p>
                <p class="product-price">¥{{ number_format($product->price) }}</p>
            </div>
        @empty
            <p class="no-products">商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection
