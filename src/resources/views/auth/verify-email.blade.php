@extends('layouts.app')


@section('content')
<div class="verify-notice" style="text-align:center; padding:30px;">
    <p>
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- 認証ページへ誘導（通常は verify.notice の画面なので文言だけ） --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" style="margin-top:15px;">
            認証はこちらから
        </button>
    </form>

    {{-- 再送リンク --}}
    <form method="POST" action="{{ route('verification.send') }}" style="margin-top:10px;">
        @csrf
        <button type="submit" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer;">
            認証メールを再送する
        </button>

    </form>
</div>
@endsection
