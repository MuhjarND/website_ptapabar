@extends('layouts.admin')
@section('title', 'Berita & Pengumuman')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Post</h2>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Post</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
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
                    <td><span class="badge {{ $post->category == 'berita' ? 'badge-info' : 'badge-warning' }}">{{ ucfirst($post->category) }}</span></td>
                    <td class="text-muted">{{ $post->user->name ?? '-' }}</td>
                    <td><span class="badge {{ $post->is_published ? 'badge-success' : 'badge-secondary' }}">{{ $post->is_published ? 'Publik' : 'Draft' }}</span></td>
                    <td class="text-muted">{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ $post->category == 'berita' ? route('post.detail', $post->slug) : route('pengumuman.detail', $post->slug) }}" target="_blank" class="btn btn-sm" style="background:#17a2b8;color:#fff;" title="Preview"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Hapus post ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada post.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $posts->links() }}</div>
@endsection
