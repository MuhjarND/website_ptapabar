@if($related->count() > 0)
    <div class="post-related">
        <h3>Artikel Terkait</h3>
        <div class="posts-grid post-related-grid">
            @foreach($related as $item)
                <a href="{{ $item->category === 'pengumuman' ? route('pengumuman.detail', $item->slug) : route('post.detail', $item->slug) }}" class="post-card">
                    <div class="card-img post-related-image">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                        @else
                            <div class="post-card-placeholder post-related-placeholder">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body post-related-body">
                        <h3 class="card-title post-related-title">{{ $item->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
