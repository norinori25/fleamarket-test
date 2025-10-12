@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-xl">
    <div class="text-center mb-6">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mx-auto w-64 h-64 object-cover rounded-xl">
    </div>

    <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>
    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
    <p class="text-lg font-semibold mb-2">価格：¥{{ number_format($product->price) }}</p>
    <p class="text-gray-500">ブランド：{{ $product->brand_name ?: 'なし' }}</p>
    <p class="text-gray-500 mb-6">状態：{{ $product->condition }}</p>

    @auth
        <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}"
           class="inline-block bg-blue-500 text-white px-6 py-3 rounded-xl hover:bg-blue-600 transition">
           購入に進む
        </a>
    @else
        <p class="text-red-500">購入するにはログインが必要です。</p>
    @endauth
</div>
@endsection
