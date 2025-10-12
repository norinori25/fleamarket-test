@extends('layouts.app')

@section('title', '支払い方法の選択')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow rounded-xl">
    <h1 class="text-xl font-bold mb-4">支払い方法を選択</h1>

    <form action="{{ route('purchase.show', ['item_id' => $product->id]) }}" method="GET">
        <div class="space-y-3 mb-6">
            <label class="block">
                <input type="radio" name="payment" value="card" checked>
                クレジットカード
            </label>
            <label class="block">
                <input type="radio" name="payment" value="convenience">
                コンビニ払い
            </label>
            <label class="block">
                <input type="radio" name="payment" value="bank">
                銀行振込
            </label>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}"
               class="text-gray-600 underline">戻る</a>

            <button type="submit"
                class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">
                保存して戻る
            </button>
        </div>
    </form>
</div>
@endsection
