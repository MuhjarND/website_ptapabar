<div class="page-content">
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Beranda</a>
        &nbsp;/&nbsp;
        @if($post->category === 'berita')
            <a href="{{ route('berita.index') }}">Berita</a>
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
        @endif
    </div>

    @if($post->image)
        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="post-detail-cover">
    @endif

    <div class="content-body">
        {!! $post->content !!}
    </div>
</div>
