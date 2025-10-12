@extends('layouts.app')

@section('title', '送付先住所の変更')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow rounded-xl">
    <h1 class="text-xl font-bold mb-4">送付先住所を変更</h1>

    <form action="{{ route('purchase.address.update', ['item_id' => $product->id]) }}" method="POST">
        @csrf
        <textarea name="address" rows="3" class="w-full border rounded p-2 mb-4" placeholder="新しい住所を入力">{{ old('address', Auth::user()->address ?? '') }}</textarea>

        <div class="flex justify-between">
            <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}"
               class="text-gray-600 underline">戻る</a>

            <button type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                保存する
            </button>
        </div>
    </form>
</div>
@endsection
