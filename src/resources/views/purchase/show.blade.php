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
        <div class="product-info">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <div class="product-detail">
                <h1>{{ $product->name }}</h1>
                <p>¥{{ number_format($product->price) }}</p>
            </div>
        </div>

        {{-- 支払い方法と住所フォーム --}}
        <form id="order-form">
            @csrf

            <div class="form-section">
                <label for="payment_method">支払い方法</label>
                <select id="payment_method" name="payment_method">
                    <option value="card">カード支払い</option>
                    <option value="konbini">コンビニ支払い</option>
                </select>

                <input type="hidden" id="payment_method_input" name="payment_method" value="card">
            </div>

            <div class="form-section">
                <label for="address">送付先</label>
                <textarea id="address" name="address" rows="3">{{ Auth::user()->address ?? '' }}</textarea>
                <button type="button" class="update-address-btn">変更を保存</button>
            </div>
        </form>
    </div>

    {{-- 右側の購入概要テーブル --}}
    <div class="right-column">
        <table class="purchase-summary">
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($product->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td id="summary-payment">カード支払い</td>
            </tr>
            <tr>
                <th>送付先住所</th>
                <td id="summary-address">{{ Auth::user()->address ?? '住所未登録' }}</td>
            </tr>
        </table>

        <form action="{{ route('checkout') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="purchase-btn">購入を確定する</button>
        </form>
    </div>
</div>

<!-- JSで動的反映 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_method');
    const display = document.getElementById('selected-payment');
    const hiddenInput = document.getElementById('payment_method_input');

    select.addEventListener('change', function() {
        const text = select.options[select.selectedIndex].text;
        display.textContent = text;
        hiddenInput.value = select.value;
    });
});
</script>
@endsection
