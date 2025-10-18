@extends('layouts.app')

@section('title', '商品詳細画面')

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="product-detail-container">
    <div class="product-image-wrapper">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
    </div>

    <div class="product-info">
        <div class="product-summary-box">
            <h1 class="product-name">{{ $product->name }}</h1>
            <p class="product-brand">ブランド名：{{ $product->brand_name ?: 'なし' }}</p>
        <p class="product-price">価格：¥{{ number_format($product->price) }}</p>
            @auth
            <form action="{{ route('favorites.toggle', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="favorite-btn">
                    {{ auth()->user()->favorites->contains($product->id) ? '★ いいね解除' : '☆ いいね' }}
                </button>
            </form>
            @endauth
        </div>

        

        @auth
        <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="btn-purchase">購入手続きへ
        </a>
        @endauth

        <h2>商品説明</h2>
        <p class="product-description">{{ $product->description }}</p>

        <h2>商品の情報</h2>
        <p>カテゴリー</p>
        <p class="product-condition">状態: {{ $product->condition }}</p>
    </div>

</div>
@endsection
