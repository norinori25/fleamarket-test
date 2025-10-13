<nav class="nav-right">
    {{-- ゲスト（未ログイン） --}}
    @guest
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('login') }}">マイページ</a>
        <a href="#" class="btn-create">出品</a>
    @endguest

    {{-- ログイン済み --}}
    @auth
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
    @csrf
    <button type="submit" class="logout-btn">ログアウト</button>
</form>
<a href="{{ route('mypage') }}">マイページ</a>
<a href="{{ route('products.create') }}" class="btn-create">出品</a>

    @endauth
</nav>