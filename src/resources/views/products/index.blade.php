@extends('layouts.app')

@section('title', 'COACHTECH | トップページ')

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
@include('components.sub-header')
<div class="product-list">
    @foreach ($products as $product)
        <div class="product-item">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <p class="product-name">{{ $product->name }}</p>
        </div>
    @endforeach
</div>
@endsection
