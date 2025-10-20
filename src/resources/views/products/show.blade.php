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
    <div class="left-column">
        <div class="product-image-wrapper">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
        </div>
    </div>

    <div class="right-column">
        <div class="product-info">

            {{-- 商品概要 --}}
            <div class="product-summary-box">
                <h1 class="product-name">{{ $product->name }}</h1>
                <p class="product-brand">{{ $product->brand_name ?: 'なし' }}</p>
                <p class="product-price">
                    ¥{{ number_format($product->price) }}<span class="tax-text">（税込）</span>
                </p>

                {{-- いいね＆コメントアイコン --}}
                <div class="interaction-buttons">
                    {{-- いいね --}}
                    <div class="icon-wrapper">
                        <form action="{{ route('favorites.toggle', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="icon-btn">
                                @if(auth()->check() && auth()->user()->favorites->contains($product->id))
                                    {{-- 塗り星SVG --}}
                                    <svg width="38" height="38" viewBox="0 0 24 24" fill="currentColor"
                                         stroke="currentColor" stroke-width="1.3">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @else
                                    {{-- 空星SVG --}}
                                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="1.3">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                        <p class="count">{{ $product->favorites_count ?? 0 }}</p>
                    </div>

                    {{-- コメント --}}
                    <div class="icon-wrapper">
                        <a href="#comment-section" class="icon-btn">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5C21 16.194 16.97 20 12 20c-1.89 0-3.64-.6-5.09-1.61L3 21l1.61-3.91C3.6 16.64 3 14.89 3 13
                                         c0-4.97 4.03-9 9-9s9 4.03 9 9z"/>
                            </svg>
                        </a>
                        <p class="count">{{ $product->comments_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- 購入ボタン --}}
            @auth
                <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="btn-purchase">購入手続きへ</a>
            @else
                <a href="{{ route('login') }}" class="btn-purchase">購入手続きへ</a>
            @endauth

            {{-- 商品説明 --}}
            <div class="product-description">
                <h2>商品説明</h2>
                <p class="product-description">{{ $product->description }}</p>
            </div>

            {{-- 商品情報 --}}
            <div class="product-info-detail">
                <h2>商品の情報</h2>
                <p class="category-line">
                    <span>カテゴリー</span>
                    <span class="category-tag">{{ $product->category->name }}</span>
                </p>

                <p class="condition-line">
                    <span>商品の状態</span>
                    <span class="product-condition">{{ $product->condition }}</span>
                </p>
            </div>

            {{-- コメントセクション --}}
            <h2 id="comment-section">コメント（{{ $product->comments_count ?? 0 }}）</h2>

            <div class="comment-list">
                @foreach ($product->comments as $comment)
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong>{{ $comment->user->name }}</strong>
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p>{{ $comment->content }}</p>
                    </div>
                @endforeach
            </div>

            {{-- コメント入力フォーム --}}
            <div class="comment-input-section">
                <label for="content">商品へのコメント</label>

                @auth
                    <form action="{{ route('comments.store', ['item_id' => $product->id]) }}" method="POST">
                @else
                    <form action="{{ route('login') }}" method="GET">
                @endauth
                    @csrf
                    <textarea name="content" rows="3" required></textarea>
                    <button type="submit" class="btn-comment">コメントを送信する</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
