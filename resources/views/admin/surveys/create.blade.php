@extends('layouts.admin')
@section('title', 'Tambah Indeks')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Indeks Survei</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.surveys.store') }}">
            @csrf
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="cth: Indeks Kepuasan Masyarakat (IKM)">
            </div>
            <div class="form-group">
                <label>Ikon (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', 'fas fa-smile') }}" placeholder="cth: fas fa-smile">
                <small style="color:#999;">Gunakan class icon dari Font Awesome, cth: fas fa-smile, fas fa-award, fas fa-check-circle</small>
            </div>
            <div class="form-group">
                <label>Nilai</label>
                <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value') }}" required style="max-width:200px" placeholder="cth: 96.25">
            </div>
            <div class="form-group">
                <label>Label Keterangan</label>
                <input type="text" name="label" class="form-control" value="{{ old('label') }}" required placeholder="cth: Sangat Baik">
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" style="max-width:120px">
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" checked> Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.surveys.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
