@extends('layouts.app')

@section('title', 'マイページ')

@php
    use Illuminate\Support\Str;
@endphp

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
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/default_user.png') }}" class="profile-img">
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
    <a href="{{ url('/mypage?page=buy') }}" class="{{ $page === 'buy' ? 'active' : '' }}">
        購入した商品
    </a>
    <a href="{{ url('/mypage?page=sell') }}" class="{{ $page === 'sell' ? 'active' : '' }}">
        出品した商品
    </a>
</div>

<!-- 商品一覧 -->
<div class="item-list">

    {{-- ✅ 購入した商品 --}}
    @if($page === 'buy')
        @forelse ($purchases as $purchase)
            <div class="item-item">
                @php
                    $img = $purchase->item->image_url;
                    $imageUrl = Str::startsWith($img, ['http://','https://'])? $img : asset('storage/item_images/' . basename($img));
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $purchase->item->name }}" class="item-image" data-item-id="{{ $purchase->item->id }}">
                <p class="item-name">{{ $purchase->item->name }}</p>

                {{-- ✅ 支払いステータス表示 --}}
                @if($purchase->status === 'pending')
                    <span class="badge pending">支払い待ち</span>
                @elseif($purchase->status === 'paid')
                    <span class="badge paid">支払い済み</span>
                @endif

                {{-- SOLD 表示 --}}
                @if($purchase->item->status === 'sold')
                    <span class="badge sold">SOLD</span>
                @endif

                <!-- ✅ モーダル(配送先) -->
                <div id="modal-{{ $purchase->item->id }}" class="modal-backdrop" style="display:none;">
                    <div class="modal-content">
                        <h3>配送先情報</h3>
                        <p>〒{{ $purchase->postal_code }}</p>
                        <p>{{ $purchase->address }}</p>
                        @if($purchase->building)
                            <p>{{ $purchase->building }}</p>
                        @endif
                        <button class="modal-close-btn">閉じる</button>
                    </div>
                </div>
            </div>
        @empty
            <p>購入した商品はありません。</p>
        @endforelse
    @endif

    {{-- ✅ 出品した商品 --}}
    @if($page === 'sell')
        @forelse ($items as $item)
            <div class="item-item">
                <a href="{{ route('items.show', $item->id) }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-image"></a>
                    <p class="item-name">{{ $item->name }}</p>

                @if($item->status === 'sold')
                    <span class="badge sold">SOLD</span>
                @endif
            </div>
        @empty
            <p>出品した商品はありません。</p>
        @endforelse
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.item-image');
    items.forEach(img => {
        img.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const modal = document.getElementById('modal-' + itemId);
            if (modal) modal.style.display = 'flex';
        });
    });

    const closeButtons = document.querySelectorAll('.modal-close-btn');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal-backdrop').style.display = 'none';
        });
    });
});
</script>

@endsection
