@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('styles')
<style>
    .dashboard-columns { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 24px; align-items: start; }
    @media (max-width: 960px) { .dashboard-columns { grid-template-columns: minmax(0, 1fr); } }
</style>
@endsection
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
        <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
        <div class="stat-number">{{ $totalPosts }}</div>
        <div class="stat-label">Total Konten</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-images"></i></div>
        <div class="stat-number">{{ $totalSliders }}</div>
        <div class="stat-label">Slider</div>
    </div>
    @if($canManageMasterData)
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-link"></i></div>
        <div class="stat-number">{{ $totalFooterLinks }}</div>
        <div class="stat-label">Tautan Footer</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-book-open"></i></div>
        <div class="stat-number">{{ $totalArticles }}</div>
        <div class="stat-label">Tinta Peradilan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-location-arrow"></i></div>
        <div class="stat-number">{{ $totalFloatingIcons }}</div>
        <div class="stat-label">Ikon Mengambang</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-number">{{ $totalAgendas }}</div>
        <div class="stat-label">Agenda Pimpinan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-inbox"></i></div>
        <div class="stat-number">{{ $totalServiceRequests }}</div>
        <div class="stat-label">Layanan Publik Masuk</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-users"></i></div>
        <div class="stat-number">{{ $totalUsers }}</div>
        <div class="stat-label">User</div>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h2>Workflow Publikasi</h2>
    </div>
    <div class="card-body">
        <div class="stats-grid" style="margin-bottom:0;">
            <div class="stat-card" style="padding:20px;">
                <div class="stat-icon purple"><i class="fas fa-pencil-ruler"></i></div>
                <div class="stat-number">{{ $workflowCounts[\App\Post::STATUS_DRAFT] }}</div>
                <div class="stat-label">Draft</div>
            </div>
            <div class="stat-card" style="padding:20px;">
                <div class="stat-icon gold"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ $workflowCounts[\App\Post::STATUS_REVIEW] }}</div>
                <div class="stat-label">Menunggu Review</div>
            </div>
            <div class="stat-card" style="padding:20px;">
                <div class="stat-icon green"><i class="fas fa-globe-asia"></i></div>
                <div class="stat-number">{{ $workflowCounts[\App\Post::STATUS_PUBLISHED] }}</div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-card" style="padding:20px;">
                <div class="stat-icon blue"><i class="fas fa-box-archive"></i></div>
                <div class="stat-number">{{ $workflowCounts[\App\Post::STATUS_ARCHIVED] }}</div>
                <div class="stat-label">Archived</div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-columns">
<div class="card">
    <div class="card-header">
        <h2>Post Terbaru</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.berita.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Berita</a>
            @if(auth()->user()->canManageAnnouncements())
            <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Pengumuman</a>
            @endif
        </div>
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
                    <td>
                        <span class="badge
                            {{ $post->workflow_status === \App\Post::STATUS_PUBLISHED ? 'badge-success' : '' }}
                            {{ $post->workflow_status === \App\Post::STATUS_REVIEW ? 'badge-warning' : '' }}
                            {{ $post->workflow_status === \App\Post::STATUS_ARCHIVED ? 'badge-secondary' : '' }}
                            {{ $post->workflow_status === \App\Post::STATUS_DRAFT || !$post->workflow_status ? 'badge-info' : '' }}">
                            {{ $post->workflow_status_label }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $post->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#999;padding:32px;">Belum ada post.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="display:grid;gap:24px;">
    <div class="card">
        <div class="card-header">
            <h2>Menunggu Review</h2>
        </div>
        <div class="card-body">
            @forelse($pendingReviewPosts as $post)
                <div style="padding:0 0 16px;margin-bottom:16px;border-bottom:1px solid #eef3ef;">
                    <div style="font-weight:600;color:#0d5c2f;margin-bottom:6px;">{{ Str::limit($post->title, 60) }}</div>
                    <div class="text-muted" style="font-size:13px;">
                        {{ ucfirst($post->category) }}
                        @if($post->user)
                            • {{ $post->user->name }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-muted">Tidak ada konten yang sedang menunggu review.</div>
            @endforelse
        </div>
    </div>

    @if($canManageMasterData)
    <div class="card">
        <div class="card-header">
            <h2>Inbox Layanan Publik</h2>
            <a href="{{ route('admin.service-requests.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-folder-open"></i> Kelola</a>
        </div>
        <div class="card-body">
            @forelse($latestServiceRequests as $serviceRequest)
                <div style="padding:0 0 16px;margin-bottom:16px;border-bottom:1px solid #eef3ef;">
                    <div style="font-weight:600;color:#0d5c2f;margin-bottom:6px;">{{ Str::limit($serviceRequest->subject, 55) }}</div>
                    <div class="text-muted" style="font-size:13px;">
                        {{ $serviceRequest->request_type_label }} • {{ $serviceRequest->name }}
                    </div>
                </div>
            @empty
                <div class="text-muted">Belum ada aspirasi, pengaduan, atau permohonan informasi.</div>
            @endforelse
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Aktivitas Terbaru</h2>
        </div>
        <div class="card-body">
            @forelse($recentActivities as $activity)
                <div style="display:flex;gap:12px;align-items:flex-start;padding:0 0 14px;margin-bottom:14px;border-bottom:1px solid #eef3ef;">
                    <div style="width:38px;height:38px;border-radius:10px;background:#f0faf3;color:#0d5c2f;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-history"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:600;color:#0d5c2f;">{{ $activity->actor_name }}</div>
                        <div style="font-size:13px;color:#4b5a50;">{{ $activity->action_label }}: {{ $activity->subject_label }}</div>
                        @if($activity->description)
                            <div class="text-muted" style="font-size:12px;margin-top:4px;">{{ $activity->description }}</div>
                        @endif
                        <div class="text-muted" style="font-size:11px;margin-top:4px;">{{ $activity->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="text-muted">Belum ada aktivitas yang tercatat.</div>
            @endforelse
        </div>
    </div>
</div>
</div>
@endsection
