<div class="sub-header">
    <div class="tabs">
        <a href="{{ route('home', ['tab' => 'all', 'keyword' => request('keyword')]) }}"
           class="{{ request('tab', 'all') === 'all' ? 'active' : '' }}">
            おすすめ
        </a>

        <a href="{{ route('home', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
           class="{{ request('tab') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>
</div>
