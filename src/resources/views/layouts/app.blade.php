<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('css')
</head>

<body>

    <header class="main-header">
        <div class="container header-inner">
            <div class="header-left">
                <div class="logo">
                    <a href="/"><img src="{{ asset('img/logo.svg') }}" alt="COACHTECH"></a>
                </div>
            </div>
            <div class="header-center">
                @hasSection('search-form')
                    @yield('search-form')
                @endif
            </div>
            <div class="header-right">
                @hasSection('nav')
                    @yield('nav')
                @endif
            </div>
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>
</body>
</html>
