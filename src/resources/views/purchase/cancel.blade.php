@extends('layouts.app')

@section('title', '購入キャンセル')

@section('content')
<div class="container">
    <h1>購入がキャンセルされました</h1>
    <p>購入手続きをキャンセルしました。商品一覧に戻る場合は<a href="{{ route('home') }}">こちら</a></p>
</div>
@endsection
