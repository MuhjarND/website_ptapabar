@extends('layouts.admin')
@section('title', 'Kategori Post')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Master Data Kategori Post</h2>
        <a href="{{ route('admin.post-categories.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Kategori</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Dipakai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-muted">{{ $category->slug }}</td>
                        <td>{{ $category->order }}</td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>{{ $category->posts_count }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.post-categories.edit', $category) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.post-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada kategori post.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $categories->links() }}</div>
@endsection
