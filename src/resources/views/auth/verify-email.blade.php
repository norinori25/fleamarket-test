@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="verify-container">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- 認証ページへ --}}
    <form method="POST" action="{{ route('verification.send') }}" class="verify-form">
        @csrf
        <button type="submit" class="verify-button">
            認証はこちらから
        </button>
    </form>

    {{-- 再送リンク --}}
    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-button">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection
