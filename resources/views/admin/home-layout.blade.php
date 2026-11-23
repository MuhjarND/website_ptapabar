@extends('layouts.admin')
@section('title', 'Editor Beranda')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin-home-layout.css') }}">
@endsection

@section('content')
<div class="card home-editor" id="home-layout-editor">
    <div class="card-header home-editor-header">
        <div>
            <h2>Tata Letak Beranda</h2>
            <p>Susun blok pada grid 12 kolom. Perubahan baru tampil setelah disimpan.</p>
        </div>
        <a class="btn home-editor-outline" href="{{ route('home') }}" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt" aria-hidden="true"></i> Buka Beranda
        </a>
    </div>
    <div class="card-body">
        <div class="home-editor-toolbar">
            <div class="home-editor-add">
                <label class="home-editor-sr-only" for="home-editor-type">Jenis blok</label>
                <select id="home-editor-type" class="form-control"></select>
                <button type="button" class="btn home-editor-outline" id="home-editor-add">
                    <i class="fas fa-plus" aria-hidden="true"></i> Tambah Blok
                </button>
            </div>
            <div class="home-editor-actions">
                @if($isCustom)
                    <form id="home-editor-reset" action="{{ route('admin.home-layout.destroy') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn home-editor-outline">Pulihkan Bawaan</button>
                    </form>
                @endif
                <form id="home-editor-save" action="{{ route('admin.home-layout.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="layout" id="home-editor-value">
                    <button type="submit" class="btn btn-primary" disabled>
                        <i class="fas fa-save" aria-hidden="true"></i> Simpan &amp; Terbitkan
                    </button>
                </form>
            </div>
        </div>

        <p class="home-editor-hint" id="home-editor-help">
            Tarik ikon panah untuk memindahkan atau menukar dua blok seukuran. Tarik sudut kanan bawah untuk mengubah ukuran.
            Ikon pensil membuka pengaturan. Di layar kecil, blok beranda ditampilkan dari atas ke bawah.
        </p>
        <div class="home-editor-scroll" role="region" tabindex="0" aria-label="Pratinjau tata letak 12 kolom" aria-describedby="home-editor-help">
            <div class="home-editor-board">
                <div class="home-editor-ruler" aria-hidden="true">
                    @for($column = 1; $column <= 12; $column++)
                        <span>{{ $column }}</span>
                    @endfor
                </div>
                <div id="home-editor-grid" class="home-editor-grid"></div>
            </div>
        </div>
        <p id="home-editor-status" class="home-editor-status" role="status"></p>
        <noscript><p>Aktifkan JavaScript untuk menggunakan editor beranda.</p></noscript>
    </div>
</div>

<template id="home-editor-tile">
    <div class="home-editor-tile">
        <div class="home-editor-tile-top">
            <span class="home-editor-move" aria-hidden="true" title="Tarik untuk memindahkan"><i class="fas fa-arrows-alt"></i></span>
            <i class="fas home-editor-tile-icon" aria-hidden="true"></i>
            <strong class="home-editor-tile-title"></strong>
            <button type="button" class="home-editor-edit" title="Edit blok"><i class="fas fa-pencil-alt" aria-hidden="true"></i></button>
        </div>
        <span class="home-editor-tile-kind"></span>
        <span class="home-editor-tile-size"></span>
        <span class="home-editor-resize" aria-hidden="true" title="Tarik untuk mengubah ukuran"><i class="fas fa-expand-alt"></i></span>
    </div>
</template>

<dialog class="home-editor-dialog" id="home-editor-dialog" aria-labelledby="home-editor-dialog-title">
    <form id="home-editor-detail">
        <h2 id="home-editor-dialog-title">Edit Blok</h2>
        <p class="home-editor-hint">Lebar diukur dalam kolom. Tinggi adalah tinggi minimum; isi blok tidak dipotong.</p>
        <div class="home-editor-fields">
            <label>Kolom awal <input class="form-control" type="number" name="x" min="1" max="12" required></label>
            <label>Baris awal <input class="form-control" type="number" name="y" min="1" max="{{ \App\Support\HomeLayout::ROWS }}" required></label>
            <label>Lebar <input class="form-control" type="number" name="w" min="1" max="12" required></label>
            <label>Tinggi <input class="form-control" type="number" name="h" min="1" max="{{ \App\Support\HomeLayout::ROWS }}" required></label>
            <label class="home-editor-full" data-field="title"><span id="home-editor-title-label">Judul</span><input class="form-control" name="title" maxlength="100"></label>
            <label class="home-editor-full" data-field="content">Isi teks <textarea class="form-control" name="content" maxlength="2000"></textarea></label>
            <label class="home-editor-full" data-field="image">URL gambar <input class="form-control" type="url" name="image" maxlength="400" placeholder="https://..."></label>
            <label class="home-editor-full" data-field="link">Tautan tujuan (opsional) <input class="form-control" type="url" name="link" maxlength="400" placeholder="https://..."></label>
        </div>
        <p class="home-editor-status is-error" id="home-editor-error" role="alert"></p>
        <div class="home-editor-dialog-actions">
            <button type="button" class="btn btn-danger" id="home-editor-remove">Hapus Blok</button>
            <button type="button" class="btn home-editor-outline" id="home-editor-cancel">Batal</button>
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </div>
    </form>
</dialog>
@endsection

@section('scripts')
@php
    $editorData = [
        'blocks' => $blocks,
        'catalog' => $catalog,
        'custom' => array_keys(\App\Support\HomeLayout::CUSTOM),
        'rows' => \App\Support\HomeLayout::ROWS,
        'limit' => \App\Support\HomeLayout::LIMIT,
    ];
@endphp
<script id="home-editor-data" type="application/json">@json($editorData)</script>
<script src="{{ asset('assets/js/admin-home-layout.js') }}"></script>
@endsection
