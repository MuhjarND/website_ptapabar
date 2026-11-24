<div class="page-content">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Beranda</a>
        &nbsp;/&nbsp;
        @if($post->category === 'berita')
            @if($post->public_url)
                <a href="{{ route('berita.index', ['kategori' => \App\PostCategory::LEADERSHIP_AGENDA]) }}">Agenda Pimpinan</a>
            @else
                <a href="{{ route('berita.index') }}">Berita</a>
            @endif
        @else
            <a href="{{ route('pengumuman.index') }}">Pengumuman</a>
        @endif
        &nbsp;/&nbsp; {{ \Illuminate\Support\Str::limit($post->title, 40) }}
    </div>

    <span class="post-detail-category">{{ ucfirst($post->category) }}</span>
    <h1>{{ $post->title }}</h1>

    <div class="post-detail-meta">
        <span><i class="far fa-calendar"></i> {{ $post->created_at->format('d F Y') }}</span>
        @if($post->user)
            <span><i class="far fa-user"></i> {{ $post->user->name }}</span>
        @elseif($post->wordpress_id)
            <span>Arsip website PTA Papua Barat</span>
        @endif
    </div>

    @if($post->image_url)
        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="post-detail-cover">
    @endif

    <div class="content-body">
        {!! $post->wordpress_id ? \App\Support\WordPressContent::archiveHtml($post->content) : $post->content !!}
    </div>
</div>
