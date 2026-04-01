@extends('layouts.admin')
@section('title', 'Tambah Aplikasi')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Aplikasi</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.site-applications.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.site-applications.partials.form', ['siteApplication' => null])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.site-applications.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
