@extends('layouts.app')

@section('title', '商品出品')

@push('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="create-container">
    <h1>商品の出品</h1>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>商品画像</label>
            <div class="image-upload-box" id="imageUploadBox">
                <img id="previewImage" class="preview-img" style="display:none;">
                <input type="file" name="image" id="imageUpload" accept="image/*" hidden>
                <button type="button" class="upload-btn" onclick="document.getElementById('imageUpload').click()">画像を選択する</button>
            </div>
        </div>


        <script>
        const imageUpload = document.getElementById('imageUpload');
        const previewImage = document.getElementById('previewImage');

        imageUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.setAttribute('src', e.target.result);
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        </script>


        <div class="item-detail">
            <h2>商品の詳細</h2>

            <div class="form-group category-group">
                <label>カテゴリー</label><br>
                @foreach($categories as $category)
                    <button type="button" class="category-btn" data-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
                <input type="hidden" name="category_ids" id="category_ids">
            </div>
            <script>
            const categoryBtns = document.querySelectorAll('.category-btn');
            const categoryInput = document.getElementById('category_ids');
            let selectedCategories = [];

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');

                    // 選択済みなら解除、未選択なら追加
                    if (selectedCategories.includes(id)) {
                        selectedCategories = selectedCategories.filter(c => c !== id);
                        btn.classList.remove('active');
                    } else {
                        selectedCategories.push(id);
                        btn.classList.add('active');
                    }

                    // 選択されたid配列をJSON文字列で送信
                    categoryInput.value = JSON.stringify(selectedCategories);
                });
            });
            </script>

            <div class="form-group">
                <label>商品の状態</label>
                <select name="condition" required>
                    <option value="" disabled selected>選択してください</option>
                    <option value="良好">良好</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="状態が悪い">状態が悪い</option>
                </select>
            </div>
        </div>

        <h2 class="section-title">商品名と説明</h2>

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label>ブランド名</label>
            <input type="text" name="brand_name" value="{{ old('brand_name') }}">
        </div>

        <div class="form-group">
            <label>商品の説明</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label>販売価格</label>
            <div class="input-with-symbol">
                <span class="price-symbol">¥</span>
                <input type="number" name="price" value="{{ old('price') }}" required>
            </div>
        </div>

        <button type="submit" class="btn-submit">出品する</button>
    </form>
</div>
@endsection