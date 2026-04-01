<h3 class="search-section-title">
    <i class="fas fa-newspaper"></i> Berita & Pengumuman ({{ $posts->total() }})
</h3>

@forelse($posts as $post)
    <a href="{{ $post->category === 'pengumuman' ? route('pengumuman.detail', $post->slug) : route('post.detail', $post->slug) }}" class="search-post-item">
        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="search-post-image">
        @endif
        <div>
            <strong class="search-post-title">{{ $post->title }}</strong>
            <p class="search-post-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt_plain, 120) }}</p>
            <span class="search-post-meta"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</span>
        </div>
    </a>
@empty
    <p class="search-empty">Tidak ditemukan hasil untuk pencarian ini.</p>
@endforelse

@if($posts->hasPages())
    <div class="pub-pagination search-pagination">
        {{ $posts->appends(['q' => $q])->links() }}
    </div>
@endif
