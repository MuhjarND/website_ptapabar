@extends('layouts.admin')
@section('title', 'Edit Halaman')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $page->title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pages.update', $page) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul Halaman</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
            </div>
            <div class="form-group">
                <label>Grup Menu</label>
                <select name="menu_group" class="form-control">
                    <option value="">-- Pilih Grup --</option>
                    @foreach($menuGroups as $key => $label)
                        <option value="{{ $key }}" {{ old('menu_group', $page->menu_group) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Halaman Induk (Parent)</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Tanpa Parent (Level Atas) --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $page->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->menu_group ? '['.strtoupper($parent->menu_group).'] ' : '' }}{{ $parent->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $page->order) }}" style="max-width:120px">
            </div>
            <div class="form-group">
                <label>Konten</label>
                <textarea name="content" class="form-control rich-editor" rows="10">{{ old('content', $page->content) }}</textarea>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" {{ $page->is_active ? 'checked' : '' }}> Aktif
                </label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.pages.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
