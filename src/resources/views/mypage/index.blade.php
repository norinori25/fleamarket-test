@extends('layouts.app')

@section('title', 'マイページ')

@push('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
<div class="mypage-container">
    <div class="mypage-header">
        <!-- 左側：プロフィール画像 + 名前 -->
        <div class="user-info-left">
            <img src="{{ $user->profile_image? asset('storage/' . $user->profile_image): asset('img/default_user.png') }}" class="profile-img">
            <p>{{ $user->name }}</p>
        </div>

        <!-- 右側：プロフィール編集ボタン -->
        <div class="user-info-right">
            <a href="{{ route('profile.edit') }}" class="edit-btn">プロフィールを編集</a>
        </div>
    </div>
</div>

    <!-- タブ切替 -->
<div class="mypage-tabs">
    <a href="{{ url('/mypage?page=sell') }}" class="{{ request('page') === 'sell' ? 'active' : '' }}">
        出品した商品
    </a>
    <a href="{{ url('/mypage?page=buy') }}" class="{{ request('page') === 'buy' ? 'active' : '' }}">
        購入した商品
    </a>
</div>


    <!-- 商品一覧 -->
    <div class="item-list">
        @forelse ($items as $item)
            <div class="item-item">
                <img src="{{ $item->image_url ?? asset('img/default_item.png') }}" alt="{{ $item->name }}">
                <p class="item-name">{{ $item->name }}</p>
            </div>
        @empty
            <p class="no-items">商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection
