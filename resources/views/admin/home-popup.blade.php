@extends('layouts.admin')
@section('title', 'Popup Beranda')
@section('content')
<div class="card">
    <div class="card-header"><h2>Popup Beranda</h2></div>
    <div class="card-body">
        <p style="margin-bottom:18px">Gambar otomatis tampil ketika pengunjung membuka beranda. Hapus gambar untuk menonaktifkan popup.</p>
        @if(!empty($popup['image']))
            <img src="{{ asset('storage/' . $popup['image']) }}" alt="{{ $popup['alt'] }}" style="display:block;max-width:100%;max-height:300px;margin-bottom:18px">
        @endif
        <form action="{{ route('admin.home-popup.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="popup-image">Gambar (JPG, PNG, WebP, atau GIF; maks. 5 MB)</label>
                <input id="popup-image" type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" @required(empty($popup['image']))>
            </div>
            <div class="form-group">
                <label for="popup-alt">Deskripsi singkat gambar untuk pembaca layar</label>
                <input id="popup-alt" name="alt" class="form-control" value="{{ old('alt', $popup['alt'] ?? '') }}" maxlength="255" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan &amp; Tampilkan</button>
        </form>
        @if(!empty($popup['image']))
            <form action="{{ route('admin.home-popup.destroy') }}" method="POST" style="margin-top:12px">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus Popup</button>
            </form>
        @endif
    </div>
</div>
@endsection
