@extends('layouts.app')

@section('title', '商品購入')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-xl">
    <h1 class="text-xl font-bold mb-4">購入内容の確認</h1>

    <div class="flex gap-6 mb-6">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-40 h-40 object-cover rounded-xl">
        <div>
            <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
            <p class="text-gray-500">価格：¥{{ number_format($product->price) }}</p>
        </div>
    </div>

    <div class="mb-4">
        <h3 class="font-semibold mb-1">送付先住所</h3>
        <p>{{ Auth::user()->address ?? '住所未登録' }}</p>
        <a href="{{ route('purchase.address.edit', ['item_id' => $product->id]) }}"
           class="text-blue-500 underline">住所を変更する</a>
    </div>

    <div class="mb-4">
        <h3 class="font-semibold mb-1">支払い方法</h3>
        <a href="{{ route('purchase.payment', ['item_id' => $product->id]) }}"
           class="text-blue-500 underline">支払い方法を選択する</a>
    </div>

    <form action="{{ route('purchase.store', ['item_id' => $product->id]) }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-green-500 text-white px-6 py-3 rounded-xl hover:bg-green-600 transition">
            購入を確定する
        </button>
    </form>
</div>
@endsection
