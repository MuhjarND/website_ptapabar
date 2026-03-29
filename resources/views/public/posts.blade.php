@extends('layouts.public')
@section('title', $title)
@section('content')
<div class="container">
    <div style="padding:40px 0;">
        <div class="section-header">
            <h2 class="section-title">{{ $title }}</h2>
        </div>
        <div class="posts-grid">
            @forelse($posts as $post)
            <a href="{{ $category == 'berita' ? route('post.detail', $post->slug) : route('pengumuman.detail', $post->slug) }}" class="post-card">
                <div class="card-img">
                    @if($post->image)
                        <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--green),var(--green-light));display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-{{ $category == 'berita' ? 'newspaper' : 'bullhorn' }}" style="font-size:36px;color:rgba(255,255,255,0.3);"></i>
                        </div>
                    @endif
                    <span class="category-badge">{{ ucfirst($category) }}</span>
                </div>
                <div class="card-body">
                    <div class="card-date"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</div>
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <p class="card-excerpt">{{ $post->excerpt }}</p>
                </div>
            </a>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:48px;color:#999;">
                <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;"></i>
                Belum ada {{ strtolower($title) }}.
            </div>
            @endforelse
        </div>
        <div class="pub-pagination">{{ $posts->links() }}</div>
    </div>
</div>
@endsection
