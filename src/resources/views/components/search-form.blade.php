<form action="{{ route('products.search') }}" method="GET" class="search-form">
    <input type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
    <button type="submit">検索</button>
</form>