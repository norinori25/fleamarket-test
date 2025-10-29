@extends('layouts.app')

@section('title', '購入完了')

@section('content')
<div class="container">
    <h1>購入が完了しました</h1>
    <p>ご購入ありがとうございました！</p>
    <a href="{{ route('home') }}">商品一覧に戻る</a>
</div>
@endsection
