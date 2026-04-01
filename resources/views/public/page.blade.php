@extends('layouts.public')
@section('title', $page->title)

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="page-layout">
        <aside class="page-sidebar">
            <div class="sidebar-menu">
                <div class="sidebar-title">
                    @if($page->menu_group)
                        {{ ucwords(str_replace('-', ' ', $page->menu_group)) }}
                    @else
                        Navigasi
                    @endif
                </div>
                @foreach($siblings as $sib)
                    <a href="{{ route('page.show', $sib->slug) }}" class="{{ $sib->id === $page->id ? 'active' : '' }}">
                        {{ $sib->title }}
                    </a>
                @endforeach
                @if($children->count() > 0)
                    @foreach($children as $child)
                        <a href="{{ route('page.show', $child->slug) }}" class="page-child-link">
                            <span class="page-child-prefix">|-</span>{{ $child->title }}
                        </a>
                    @endforeach
                @endif
            </div>
        </aside>

        <div class="page-content">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                @foreach($breadcrumbs as $bc)
                    &nbsp;/&nbsp; <a href="{{ route('page.show', $bc->slug) }}">{{ $bc->title }}</a>
                @endforeach
                &nbsp;/&nbsp; {{ $page->title }}
            </div>
            <h1>{{ $page->title }}</h1>
            <div class="content-body">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>
@endsection
