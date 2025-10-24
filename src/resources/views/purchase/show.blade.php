@extends('layouts.app')

@section('title', '商品購入')

@push('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('search-form')
@include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="purchase-container">

    <div class="left-column">
        {{-- 商品情報 --}}
        <div class="item-info">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
            <div class="item-detail">
                <h1>{{ $item->name }}</h1>
                <p>¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        {{-- 横線 --}}
        <div class="divider"></div>

        {{-- 支払い方法 --}}
        <div class="payment-section">
            <h2>支払い方法</h2>
        </div>
         <div class="form-section">
            <select name="payment_method" id="payment_method" class="payment-select" required>
    <option value="" disabled selected>選択してください</option>
    <option value="konbini">コンビニ払い</option>
    <option value="card">カード払い</option>
</select>


        </div>

        {{-- 横線 --}}
        <div class="divider"></div>

        {{-- 住所セクション --}}
        <div class="address-section">
            <div class="address-header">
                <h2>配送先</h2>
            </div>
            <div class="address-edit-link">
                <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}">変更する</a>
            </div>
        </div>
        <div class="address-content">
            <p>〒{{ $shippingAddress['postal_code'] }}</p>
            <p>{{ $shippingAddress['address'] }}</p>
            @if(!empty($shippingAddress['building']))
                <p>{{ $shippingAddress['building'] }}</p>
            @endif
        </div>

        {{-- 横線 --}}
        <div class="divider"></div>
    </div>

    {{-- 右側の購入概要テーブル --}}
    <div class="right-column">
        <table class="purchase-summary">
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="summary-payment">選択してください</td>
            </tr>
        </table>

        <form action="{{ route('checkout') }}" method="POST">
    @csrf
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    <input type="hidden" name="payment_method" id="payment_method_hidden" value="card">
    <button type="submit" class="purchase-btn">購入する</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_method');
    const hidden = document.getElementById('payment_method_hidden');
    const summary = document.getElementById('summary-payment');

    // 初期表示
    if (select.value) {
        summary.textContent = select.options[select.selectedIndex].text;
        hidden.value = select.value;
    }

    select.addEventListener('change', function() {
        summary.textContent = select.options[select.selectedIndex].text;
        hidden.value = select.value; // ここで hidden に反映
    });
});
</script>

@endsection
