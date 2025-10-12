<div class="sub-header">
    <div class="tabs">
        <a href="{{ url('/') }}" class="{{ request('tab', 'all') === 'all' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=mylist') }}" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>
</div>