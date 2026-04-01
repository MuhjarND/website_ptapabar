@extends('layouts.public')
@section('title', 'Tinta Peradilan')

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="section section-white">
    <div class="container">
        <div class="content-shell-wide">
            <div class="section-header">
                <h1 class="section-title">Tinta Peradilan</h1>
            </div>
            <div class="archive-filter-bar">
                <a href="{{ route('article.index') }}" class="archive-filter-button {{ $activeArticleCategory ? '' : 'is-active' }}">Semua</a>
                @foreach($articleCategories as $value => $label)
                    <a href="{{ route('article.index', ['article_category' => $value]) }}" class="archive-filter-button {{ $activeArticleCategory === $value ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="article-archive-panel">
                <ul class="pengumuman-list">
                    @forelse($articles as $article)
                        <li>
                            <a href="{{ $article->target_url }}" class="pengumuman-item" {{ $article->target_is_external ? 'target=_blank rel=noopener' : '' }}>
                                <div class="date-box">
                                    <span class="day">{{ $article->created_at->format('d') }}</span>
                                    <span class="month">{{ $article->created_at->format('M') }}</span>
                                </div>
                                <div class="item-content">
                                    <h4>{{ $article->title }}</h4>
                                    @if($article->article_category_label)
                                        <div class="section-category-pill announcement-category-pill">{{ $article->article_category_label }}</div>
                                    @endif
                                    <p>{{ \Illuminate\Support\Str::limit($article->excerpt_plain, 140) }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="archive-empty"><i class="fas fa-book-open"></i>Belum ada artikel.</li>
                    @endforelse
                </ul>
            </div>

            <div class="archive-pagination">{{ $articles->appends(request()->query())->links() }}</div>
        </div>
    </div>
</section>
@endsection
