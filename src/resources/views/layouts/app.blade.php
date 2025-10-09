<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">

</head>
<body>
    <!-- ヘッダー -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/"><img src="{{ asset('img/logo.svg') }}" alt="COACHTECH"></a>
            </div>
            @if (!Request::is('register') && !Request::is('login'))
                <div class="header-search">
                    @yield('search')
                </div>
                <nav class="nav-right">
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="{{ route('mypage') }}">マイページ</a>
                    <a href="{{ route('products.create') }}" class="btn-create">出品</a>
                </nav>
            @endif
        </div>
    </header>

    <!-- サブヘッダー -->
    @if (!Request::is('register') && !Request::is('login'))
    <div class="sub-header">
        <div class="tabs">
            <!-- おすすめ（トップページ） -->
            <a href="{{ url('/') }}" class="{{ request('tab', 'all') === 'all' ? 'active' : '' }}">おすすめ</a>
            <!-- マイリスト -->
            <a href="{{ url('/?tab=mylist') }}" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
        </div>
    </div>
    @endif

    <main class="content">
        @yield('content')
    </main>
</body>
</html>