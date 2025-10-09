@extends('layouts.app')

@section('title', 'COACHTECH | トップページ')

@section('search')
<form action="{{ route('products.search') }}" method="GET" class="search-form">
    <input type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
    <button type="submit">検索</button>
</form>
@endsection

@section('content')
<div class="product-list">
    @foreach ($products as $product)
        <div class="product-item">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <p class="product-name">{{ $product->name }}</p>
        </div>
    @endforeach
</div>
@endsection
