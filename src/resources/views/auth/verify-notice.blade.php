@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="verify-container">
    <p class="verify-message">
        メール認証が完了しました。<br>
        プロフィール設定を行ってください。
    </p>

    <a href="{{ route('profile.edit') }}" class="verify-button">
        プロフィール設定へ進む
    </a>
</div>
@endsection
