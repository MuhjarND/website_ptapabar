@extends('layouts.admin')
@section('title', 'Jenis Dokumen')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Jenis Dokumen</h2>
        <a href="{{ route('admin.pages.create', ['menu_group' => \App\TransparencyDocument::PAGE_GROUP]) }}" class="btn btn-gold btn-sm">Tambah Jenis</a>
    </div>
    <div class="card-body">
        Setiap halaman Transparansi menjadi satu jenis dokumen. Gunakan halaman yang sudah ada agar arsip tidak terpisah.
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr><th>Jenis / Halaman</th><th>Dokumen</th><th>Status halaman</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>{{ $page->title }}</td>
                        <td>{{ $page->transparency_documents_count }}</td>
                        <td><span class="badge {{ $page->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $page->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.transparency-documents.index', ['page_id' => $page->id]) }}" class="btn btn-primary btn-sm">Buka Arsip</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm">Edit Halaman</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada halaman Transparansi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $pages->links() }}</div>
@endsection
