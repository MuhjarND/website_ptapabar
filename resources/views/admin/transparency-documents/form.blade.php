@extends('layouts.admin')
@section('title', $document->exists ? 'Edit Dokumen Transparansi' : 'Tambah Dokumen Transparansi')
@section('content')
<div class="card">
    <div class="card-header"><h2>{{ $document->exists ? 'Edit Dokumen Transparansi' : 'Tambah Dokumen Transparansi' }}</h2></div>
    <div class="card-body">
        @if($pages->isEmpty())
            <p>Buat halaman dalam kelompok Transparansi di menu Halaman sebelum menambahkan dokumen.</p>
        @else
            <form method="POST" enctype="multipart/form-data"
                  action="{{ $document->exists ? route('admin.transparency-documents.update', $document) : route('admin.transparency-documents.store') }}">
                @csrf
                @if($document->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="title">Judul dokumen</label>
                    <input id="title" name="title" class="form-control" value="{{ old('title', $document->title) }}" maxlength="255" required>
                </div>
                <div class="form-group">
                    <label for="page_id">Kategori / halaman tujuan</label>
                    <select id="page_id" name="page_id" class="form-control" required>
                        <option value="">Pilih halaman Transparansi</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}" {{ (string) old('page_id', $document->page_id) === (string) $page->id ? 'selected' : '' }}>
                                {{ $page->title }}{{ $page->is_active ? '' : ' (Nonaktif)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Tahun dokumen / tahun awal periode</label>
                    <input type="number" id="year" name="year" class="form-control" min="1900" max="2100" value="{{ old('year', $document->year) }}" required>
                </div>
                <div class="form-group">
                    <label for="period">Periode (opsional)</label>
                    <input id="period" name="period" class="form-control" value="{{ old('period', $document->period) }}" maxlength="80" placeholder="Contoh: 2025-2029 atau Semester I 2026">
                </div>
                <div class="form-group">
                    <label for="version">Versi (opsional)</label>
                    <input id="version" name="version" class="form-control" value="{{ old('version', $document->version) }}" maxlength="80" placeholder="Contoh: Final atau Reviu 1">
                </div>
                <div class="form-group">
                    <label for="budget_code">Kode anggaran (opsional)</label>
                    <input id="budget_code" name="budget_code" class="form-control" value="{{ old('budget_code', $document->budget_code) }}" maxlength="30" placeholder="Contoh: DIPA 01 atau 526718">
                </div>
                <div class="form-group">
                    <label for="source">Sumber dokumen (catatan internal)</label>
                    <input id="source" name="source" class="form-control" value="{{ old('source', $document->source) }}" maxlength="255" required placeholder="Contoh: Bagian Perencanaan">
                </div>
                <div class="form-group">
                    <label for="pdf_file">Unggah PDF (maks. 10 MB)</label>
                    <input type="file" id="pdf_file" name="pdf_file" class="form-control" accept="application/pdf,.pdf">
                </div>
                <div class="form-group">
                    <label for="external_url">Atau tautan dokumen HTTPS</label>
                    <input type="url" id="external_url" name="external_url" class="form-control" value="{{ old('external_url') }}" maxlength="2048" placeholder="https://">
                    @if($document->pdf_path || $document->external_url)
                        <p><a href="{{ $document->external_url ?: route('admin.transparency-documents.download', $document) }}">Cek sumber saat ini</a>. Kosongkan kedua pilihan untuk mempertahankan sumber.</p>
                    @endif
                </div>
                <p>Simpan sebagai draf, lalu periksa isi, periode, dan akses dokumen sebelum memilih Terbitkan. Mengedit dokumen terbit akan mengembalikannya ke draf.</p>
                <button type="submit" class="btn btn-gold">Simpan Draf</button>
                <a href="{{ route('admin.transparency-documents.index') }}" class="btn btn-primary">Kembali</a>
            </form>
        @endif
    </div>
</div>
@endsection
