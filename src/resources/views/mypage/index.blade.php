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
            <img src="{{ $item->image_url ?? asset('img/default_item.png') }}" alt="{{ $item->name }}" class="item-image" data-item-id="{{ $item->id }}">
            <p class="item-name">{{ $item->name }}</p>
            @if($item->status === 'sold')
                <span class="badge sold">SOLD</span>
            @endif
        </div>

        @if($page === 'buy' && $item->pivot && $item->pivot->address)
            <!-- モーダル -->
            <div id="modal-{{ $item->id }}" class="modal-backdrop" style="display:none;">
                <div class="modal-content">
                    <h3>配送先情報</h3>
                    <p>〒{{ $item->pivot->address->postal_code }}</p>
                    <p>{{ $item->pivot->address->address }}</p>
                    @if($item->pivot->address->building)
                        <p>{{ $item->pivot->address->building }}</p>
                    @endif
                    <button class="modal-close-btn">閉じる</button>
                </div>
            </div>
        @endif
        @empty
            <p class="no-items">商品がありません。</p>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 商品画像クリックでモーダル表示
    const items = document.querySelectorAll('.item-image');
    items.forEach(img => {
        img.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const modal = document.getElementById('modal-' + itemId);
            if(modal) modal.style.display = 'flex';
        });
    });

    // モーダル閉じるボタン
    const closeButtons = document.querySelectorAll('.modal-close-btn');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal-backdrop').style.display = 'none';
        });
    });
});
</script>

</div>
@endsection
