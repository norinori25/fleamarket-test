<form action="{{ route('home') }}" method="GET" class="search-form">
<input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
<input type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
    <button type="submit">検索</button>
</form>