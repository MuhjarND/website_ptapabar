@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
        <div class="stat-number">{{ $totalPages }}</div>
        <div class="stat-label">Total Halaman</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-newspaper"></i></div>
        <div class="stat-number">{{ $totalBerita }}</div>
        <div class="stat-label">Berita</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-bullhorn"></i></div>
        <div class="stat-number">{{ $totalPengumuman }}</div>
        <div class="stat-label">Pengumuman</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-images"></i></div>
        <div class="stat-number">{{ $totalSliders }}</div>
        <div class="stat-label">Slider</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Post Terbaru</h2>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Baru</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestPosts as $post)
                <tr>
                    <td>{{ Str::limit($post->title, 60) }}</td>
                    <td><span class="badge {{ $post->category == 'berita' ? 'badge-info' : 'badge-warning' }}">{{ ucfirst($post->category) }}</span></td>
                    <td><span class="badge {{ $post->is_published ? 'badge-success' : 'badge-secondary' }}">{{ $post->is_published ? 'Publik' : 'Draft' }}</span></td>
                    <td class="text-muted">{{ $post->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#999;padding:32px;">Belum ada post.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
