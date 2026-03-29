@extends('layouts.public')
@section('title', 'Hasil Pencarian')
@section('content')
<div class="container">
    <div style="padding:40px 0; max-width:800px; margin:0 auto;">
        <div class="page-content">
            <h1 style="margin-bottom:4px;">Hasil Pencarian</h1>
            <p style="color:var(--text-light);font-size:14px;margin-bottom:24px;">
                Menampilkan hasil untuk: <strong>"{{ $q }}"</strong>
            </p>

            @if($pages->count() > 0)
            <h3 style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:12px;">
                <i class="fas fa-file-alt" style="margin-right:6px;"></i> Halaman ({{ $pages->count() }})
            </h3>
            <div style="margin-bottom:24px;">
                @foreach($pages as $page)
                <a href="{{ route('page.show', $page->slug) }}" style="display:block;padding:12px 16px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;transition:0.2s;">
                    <strong style="color:var(--green);">{{ $page->title }}</strong>
                    <p style="font-size:12px;color:var(--text-light);margin-top:4px;">{{ Str::limit(strip_tags($page->content), 120) }}</p>
                </a>
                @endforeach
            </div>
            @endif

            <h3 style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:12px;">
                <i class="fas fa-newspaper" style="margin-right:6px;"></i> Berita & Pengumuman ({{ $posts->total() }})
            </h3>
            @forelse($posts as $post)
            <a href="{{ route('post.detail', $post->slug) }}" style="display:flex;gap:16px;padding:16px 0;border-bottom:1px solid var(--border);transition:0.2s;">
                @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" style="width:80px;height:60px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                @endif
                <div>
                    <strong style="color:var(--green);">{{ $post->title }}</strong>
                    <p style="font-size:12px;color:var(--text-light);margin-top:4px;">{{ Str::limit($post->excerpt, 120) }}</p>
                    <span style="font-size:11px;color:var(--text-light);"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</span>
                </div>
            </a>
            @empty
            <p style="text-align:center;padding:32px;color:#999;">Tidak ditemukan hasil untuk pencarian ini.</p>
            @endforelse

            @if($posts->hasPages())
            <div class="pub-pagination" style="margin-top:24px;">
                {{ $posts->appends(['q' => $q])->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
