@extends('layouts.app')

@section('title', '商品出品')

@section('content')
<div class="create-container">
    <h1>商品の出品</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>画像URL</label>
            <input type="text" name="image_url" value="{{ old('image_url') }}" required>
        </div>

        <div class="prodduct-detail">
            <p>商品の詳細</p>
            <div>
                <label>カテゴリー</label>
                <div class="category-buttons">
                    @foreach($categories as $category)
                        <label style="margin-right:8px; display:inline-block; cursor:pointer;">
                            <input type="radio" name="category_id" value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'checked' : '' }}>
                            <span style="padding:5px 10px; border:1px solid #ccc; border-radius:5px;">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('category_id') <p style="color:red">{{ $message }}</p> @enderror
            </div>

            <!-- 状態（セレクトボックス） -->
            <div style="margin-top:10px;">
                <label>商品の状態</label>
                <select name="condition">
                    @php
                        $conditions = [
                            '良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'
                        ];
                    @endphp

                    @foreach($conditions as $cond)
                        <option value="{{ $cond }}" {{ old('condition') == $cond ? 'selected' : '' }}>
                            {{ $cond }}
                        </option>
                    @endforeach
                </select>
                @error('condition') <p style="color:red">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
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
            <label>販売価格</label>
            <input type="number" name="price" value="{{ old('price') }}" required>
        </div>

        <button type="submit" class="btn">出品する</button>
    </form>
</div>
@endsection
