@extends('layouts.admin')
@section('title', 'Edit Indeks')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $survey->title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.surveys.update', $survey) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $survey->title) }}" required>
            </div>
            <div class="form-group">
                <label>Ikon (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $survey->icon) }}">
                <small style="color:#999;">cth: fas fa-smile, fas fa-award, fas fa-check-circle</small>
            </div>
            <div class="form-group">
                <label>Nilai</label>
                <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $survey->value) }}" required style="max-width:200px">
            </div>
            <div class="form-group">
                <label>Label Keterangan</label>
                <input type="text" name="label" class="form-control" value="{{ old('label', $survey->label) }}" required>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $survey->order) }}" style="max-width:120px">
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" {{ $survey->is_active ? 'checked' : '' }}> Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.surveys.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
