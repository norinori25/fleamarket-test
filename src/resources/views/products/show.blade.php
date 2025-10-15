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

    <h1 class="product-name">{{ $product->name }}</h1>
    <p class="product-description">{{ $product->description }}</p>
    <p class="product-price">価格：¥{{ number_format($product->price) }}</p>
    <p class="product-brand">ブランド：{{ $product->brand_name ?: 'なし' }}</p>
    <p class="product-condition">状態：{{ $product->condition }}</p>

    @auth
        <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="btn-purchase">
           購入に進む
        </a>
    @else
        <p class="text-red-500">購入するにはログインが必要です。</p>
    @endauth
</div>
@endsection
