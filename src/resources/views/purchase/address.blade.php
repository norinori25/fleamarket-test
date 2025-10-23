@extends('layouts.app')

@section('title', '住所の変更')

@push('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('search-form')
@include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
<div class="address-edit-container">
    <h1>住所の変更</h1>

    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $shippingAddress['postal_code']) }}">
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $shippingAddress['address']) }}">
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $shippingAddress['building']) }}">
        </div>

        <button type="submit" class="btn">更新する</button>
    </form>
</div>
@endsection

