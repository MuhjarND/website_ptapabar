@extends('layouts.admin')
@section('title', 'Kelola Halaman')
@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pages.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="keyword">Cari Judul</label>
                        <input
                            type="text"
                            id="keyword"
                            name="keyword"
                            class="form-control"
                            value="{{ request('keyword') }}"
                            placeholder="Cari halaman..."
                        >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="menu_group">Grup Menu</label>
                        <select id="menu_group" name="menu_group" class="form-control">
                            <option value="">Semua Grup</option>
                            @foreach($menuGroups as $key => $label)
                                <option value="{{ $key }}" {{ request('menu_group') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="structure">Tipe Halaman</label>
                        <select id="structure" name="structure" class="form-control">
                            <option value="">Semua Tipe</option>
                            @foreach($structureOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('structure') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.pages.index') }}" class="btn" style="background:#e9ecef">Reset</a>
            </div>
        </form>
    </div>
</div>

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
                            <span style="color:#999">|-</span>
                        @endif
                        {{ $page->title }}
                    </td>
                    <td><span class="badge badge-info">{{ $menuGroups[$page->menu_group] ?? '-' }}</span></td>
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
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Tidak ada halaman yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $pages->links() }}</div>
@endsection
