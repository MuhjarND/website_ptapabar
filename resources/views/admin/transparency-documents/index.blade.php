@extends('layouts.admin')
@section('title', 'Arsip Dokumen')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Arsip Dokumen Transparansi</h2>
        <a href="{{ route('admin.transparency-documents.create', $pageId ? ['page_id' => $pageId] : []) }}" class="btn btn-gold">Tambah Dokumen</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.transparency-documents.index') }}">
            <div class="form-group">
                <label for="page_id">Filter halaman Transparansi</label>
                <select id="page_id" name="page_id" class="form-control">
                    <option value="">Semua halaman</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}" {{ $pageId === $page->id ? 'selected' : '' }}>{{ $page->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
        </form>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Judul dan sumber</th>
                    <th>Kategori / halaman</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($documents as $document)
                <tr>
                    <td><strong>{{ $document->title }}</strong><br>{{ $document->source }}</td>
                    <td>{{ optional($document->page)->title ?? 'Halaman tidak tersedia' }}</td>
                    <td>
                        {{ $document->period ?: $document->year }}
                        @if($document->budget_code) <br>{{ $document->budget_code }} @endif
                        @if($document->version) <br>{{ $document->version }} @endif
                    </td>
                    <td>
                        @if($document->published_at)
                            <span class="badge badge-success">Terbit</span>
                            @if(!$document->page || !$document->page->is_active || $document->page->menu_group !== \App\TransparencyDocument::PAGE_GROUP)
                                <br>Halaman tujuan tidak tampil
                            @endif
                        @else
                            <span class="badge badge-secondary">Draf</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            @if($document->pdf_path || $document->external_url)
                                <a href="{{ $document->external_url ?: route('admin.transparency-documents.download', $document) }}" class="btn btn-sm btn-primary">Cek Sumber</a>
                            @endif
                            <a href="{{ route('admin.transparency-documents.edit', $document) }}" class="btn btn-sm btn-primary">Edit</a>
                            @if($document->published_at)
                                <form method="POST" action="{{ route('admin.transparency-documents.unpublish', $document) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Tarik ke Draf</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.transparency-documents.publish', $document) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Terbitkan</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada dokumen untuk ditampilkan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $documents->links() }}</div>
@endsection
