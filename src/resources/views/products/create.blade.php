@extends('layouts.app')

@section('title', '商品出品')

@section('content')
<div class="create-container">
    <h1>商品を出品する</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label>価格</label>
            <input type="number" name="price" value="{{ old('price') }}" required>
        </div>

        <div class="form-group">
            <label>ブランド名</label>
            <input type="text" name="brand_name" value="{{ old('brand_name') }}">
        </div>

        <div class="form-group">
            <label>説明</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label>画像URL</label>
            <input type="text" name="image_url" value="{{ old('image_url') }}" required>
        </div>

        <button type="submit" class="btn">出品する</button>
    </form>
</div>
@endsection
