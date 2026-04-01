@extends('layouts.admin')
@section('title', $pageTitle)
@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ $pageHeading }}</h2>
        <a href="{{ $createRoute }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> {{ $createLabel }}</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $indexRoute }}" class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:20px;">
            @if($showCategoryFilter)
            <div class="form-group mb-0" style="min-width:220px;flex:1;">
                <label>Jenis Publikasi</label>
                <select name="category" class="form-control">
                    <option value="">Semua jenis</option>
                    <option value="berita" {{ request('category') === 'berita' ? 'selected' : '' }}>Berita</option>
                    <option value="pengumuman" {{ request('category') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                </select>
            </div>
            @endif
            @if($showNewsCategoryFilter)
            <div class="form-group mb-0" style="min-width:220px;flex:1;">
                <label>Kategori Berita</label>
                <select name="post_category_id" class="form-control">
                    <option value="">Semua kategori berita</option>
                    @foreach($postCategories as $postCategory)
                        <option value="{{ $postCategory->id }}" {{ (string) request('post_category_id') === (string) $postCategory->id ? 'selected' : '' }}>
                            {{ $postCategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($showNewsScopeFilter)
            <div class="form-group mb-0" style="min-width:220px;flex:1;">
                <label>Kelompok Berita</label>
                <select name="news_scope" class="form-control">
                    <option value="">Semua kelompok berita</option>
                    @foreach(\App\Post::newsScopeOptions() as $value => $label)
                        <option value="{{ $value }}" {{ request('news_scope') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($showAnnouncementCategoryFilter)
            <div class="form-group mb-0" style="min-width:220px;flex:1;">
                <label>Kategori Pengumuman</label>
                <select name="announcement_category" class="form-control">
                    <option value="">Semua kategori pengumuman</option>
                    @foreach(\App\Post::announcementCategoryOptions() as $value => $label)
                        <option value="{{ $value }}" {{ request('announcement_category') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group mb-0" style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    @if($showCategoryFilter)
                    <th>Jenis</th>
                    @endif
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>{{ Str::limit($post->title, 50) }}</td>
                    @if($showCategoryFilter)
                    <td><span class="badge {{ $post->category == 'berita' ? 'badge-info' : 'badge-warning' }}">{{ ucfirst($post->category) }}</span></td>
                    @endif
                    <td class="text-muted">
                        @if($post->category === 'berita')
                            <div>{{ $post->postCategory->name ?? '-' }}</div>
                            @if($post->news_scope_label)
                                <div style="margin-top:4px;">
                                    <span class="badge badge-info">{{ $post->news_scope_label }}</span>
                                </div>
                            @endif
                        @elseif($showCategoryFilter)
                            <div>{{ $post->announcement_category_label ?? '-' }}</div>
                        @else
                            <div>{{ $post->announcement_category_label ?? '-' }}</div>
                        @endif
                        @if($showCategoryFilter && $post->announcement_category_label)
                            <div style="margin-top:4px;">
                                <span class="badge badge-info">{{ $post->announcement_category_label }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="text-muted">{{ $post->user->name ?? '-' }}</td>
                    <td><span class="badge {{ $post->is_published ? 'badge-success' : 'badge-secondary' }}">{{ $post->is_published ? 'Publik' : 'Draft' }}</span></td>
                    <td class="text-muted">{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ $post->category == 'berita' ? route('post.detail', $post->slug) : route('pengumuman.detail', $post->slug) }}" target="_blank" class="btn btn-sm" style="background:#17a2b8;color:#fff;" title="Preview"><i class="fas fa-eye"></i></a>
                            <a href="{{ $post->category == 'berita' ? route('admin.berita.edit', $post) : route('admin.pengumuman.edit', $post) }}" class="btn btn-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ $post->category == 'berita' ? route('admin.berita.destroy', $post) : route('admin.pengumuman.destroy', $post) }}" onsubmit="return confirm('Hapus {{ $itemLabel }} ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $showCategoryFilter ? 7 : 6 }}" style="text-align:center;color:#999;padding:32px;">Belum ada {{ $itemLabel }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $posts->appends(request()->query())->links() }}</div>
@endsection
