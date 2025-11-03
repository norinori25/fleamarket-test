@extends('layouts.app')

@section('content')
<div class="verify-notice">

    <h2>メール認証がまだ完了していません</h2>
    <p>ご登録のメールアドレスに認証メールを送信しました。</p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit">認証はこちらから</button>
    </form>
</div>
@endsection
