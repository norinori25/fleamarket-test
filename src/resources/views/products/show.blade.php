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
            <div class="product-summary-box">
                <h1 class="product-name">{{ $product->name }}</h1>
                <p class="product-brand">{{ $product->brand_name ?: 'なし' }}</p>
                <p class="product-price">¥{{ number_format($product->price) }}<span class="tax-text">（税込み）</span></p>
                {{-- いいね＆コメントアイコン --}}
                <div class="interaction-buttons">
                    {{-- いいね --}}
                    <div class="icon-wrapper">
                        <form action="{{ route('favorites.toggle', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="icon-btn">
                                @if(auth()->check())
                                    {{ auth()->user()->favorites->contains($product->id) ? '★' : '☆' }}
                                @else
                                    ☆
                                @endif
                            </button>
                        </form>
                        <p class="count">{{ $product->favorites_count ?? 0 }}</p>
                    </div>

                    {{-- コメント --}}
                    <div class="icon-wrapper">
                        @if(auth()->check())
                            <a href="#comment-section" class="icon-btn">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 11.5C21 16.194 16.97 20 12 20c-1.89 0-3.64-.6-5.09-1.61L3 21l1.61-3.91C3.6 16.64 3 14.89 3 13c0-4.97 4.03-9 9-9s9 4.03 9 9z"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="icon-btn">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 11.5C21 16.194 16.97 20 12 20c-1.89 0-3.64-.6-5.09-1.61L3 21l1.61-3.91C3.6 16.64 3 14.89 3 13c0-4.97 4.03-9 9-9s9 4.03 9 9z"/>
                                </svg>
                            </a>
                        @endif
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

            <h2>商品説明</h2>
            <p class="product-description">{{ $product->description }}</p>

            <h2>商品の情報</h2>
            <p>カテゴリー<span class="category-tag">{{ $product->category->name }}</span></p>
            <p><span class="product-condition">商品の状態</span>{{ $product->condition }}</p>

            {{-- コメント欄 --}}
            <h2 id="comment-section">コメント（{{ $product->comments_count ?? 0 }}）</h2>

            <div class="comment-list">
                @foreach ($product->comments as $comment)
                    <div class="comment-item @if($comment->user->is_admin) admin-comment @endif">
                        <div class="comment-header">
                            <strong>{{ $comment->user->name }}</strong>
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p>{{ $comment->content }}</p>
                    </div>
                @endforeach
            </div>

            @auth
            <div class="comment-input-section">
                <label for="content">商品へのコメント</label>
                <form action="{{ route('comments.store', $product->id) }}" method="POST">
                 @csrf
                    <textarea name="content" rows="3" placeholder="コメントを書く..." required></textarea>
                    <button type="submit" class="btn-comment">送信</button>
                </form>
            </div>
            @endauth

            {{-- コメント欄 --}}
            <h2 id="comment-section">コメント</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @auth
            <form action="{{ route('comments.store', $product->id) }}" method="POST">
                @csrf
                <textarea name="content" rows="3" placeholder="コメントを書く..." required></textarea>
                <button type="submit" class="btn-comment">送信</button>
            </form>
            @else
            <p><a href="{{ route('login') }}">ログインしてコメントを投稿</a></p>
            @endauth

            <div class="comment-list">
                @foreach ($product->comments as $comment)
                    <div class="comment-item">
                        <strong>{{ $comment->user->name }}</strong>
                        <p>{{ $comment->content }}</p>
                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
