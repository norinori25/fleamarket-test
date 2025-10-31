@extends('layouts.app')

@section('title', 'COACHTECH | トップページ')

@section('search-form')
    @include('components.search-form')
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')
@include('components.sub-header')
<div class="item-list">
    @foreach ($items as $item)
        <div class="item-item">
            <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
                <p class="item-name">{{ $item->name }}</p>
            </a>
            @if($item->status === 'sold')
                <span class="badge sold">SOLD</span>
            @endif
        </div>
    @endforeach
</div>
@endsection
