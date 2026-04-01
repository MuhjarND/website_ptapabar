@extends('layouts.public')
@section('title', $title)

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="content-shell-wide">
        <div class="section-header">
            <h2 class="section-title">{{ $title }}</h2>
        </div>
        @if($category === 'berita')
            <div class="archive-filter-bar">
                <a href="{{ route('berita.index') }}" class="archive-filter-button {{ !$activeNewsScope ? 'is-active' : '' }}">
                    Semua
                </a>
                @foreach($newsScopes as $value => $label)
                    <a
                        href="{{ route('berita.index', ['news_scope' => $value]) }}"
                        class="archive-filter-button {{ $activeNewsScope === $value ? 'is-active' : '' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        @elseif($category === 'pengumuman')
            <div class="archive-filter-bar">
                <a href="{{ route('pengumuman.index') }}" class="archive-filter-button {{ !$activeAnnouncementCategory ? 'is-active' : '' }}">
                    Semua
                </a>
                @foreach($announcementCategories as $value => $label)
                    <a
                        href="{{ route('pengumuman.index', ['announcement_category' => $value]) }}"
                        class="archive-filter-button {{ $activeAnnouncementCategory === $value ? 'is-active' : '' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        @endif
        <div class="posts-grid">
            @forelse($posts as $post)
                <a href="{{ $category === 'berita' ? route('post.detail', $post->slug) : route('pengumuman.detail', $post->slug) }}" class="post-card">
                    <div class="card-img">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                        @else
                            <div class="post-card-placeholder">
                                <i class="fas fa-{{ $category === 'berita' ? 'newspaper' : 'bullhorn' }}"></i>
                            </div>
                        @endif
                        <span class="category-badge">
                            {{ $category === 'pengumuman' ? ($post->announcement_category_label ?: 'Pengumuman') : ucfirst($category) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="card-date"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</div>
                        <h3 class="card-title">{{ $post->title }}</h3>
                        @if($category === 'berita' && $post->news_scope_label)
                            <div class="archive-post-tag">{{ $post->news_scope_label }}</div>
                        @elseif($category === 'pengumuman' && $post->announcement_category_label)
                            <div class="archive-post-tag">{{ $post->announcement_category_label }}</div>
                        @endif
                        <p class="card-excerpt">{{ $post->excerpt_plain }}</p>
                    </div>
                </a>
            @empty
                <div class="archive-empty">
                    <i class="fas fa-inbox"></i>
                    Belum ada {{ strtolower($title) }}.
                </div>
            @endforelse
        </div>
        <div class="pub-pagination archive-pagination">{{ $posts->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection
