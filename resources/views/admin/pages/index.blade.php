@extends('layouts.admin')
@section('title', 'Kelola Halaman')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Halaman</h2>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Halaman</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Grup Menu</th>
                    <th>Parent</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>
                        @if($page->parent_id)
                            <span style="color:#999">└─</span>
                        @endif
                        {{ $page->title }}
                    </td>
                    <td><span class="badge badge-info">{{ $page->menu_group ?? '-' }}</span></td>
                    <td class="text-muted">{{ $page->parent->title ?? '-' }}</td>
                    <td>{{ $page->order }}</td>
                    <td><span class="badge {{ $page->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $page->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Hapus halaman ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada halaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $pages->links() }}</div>
@endsection
