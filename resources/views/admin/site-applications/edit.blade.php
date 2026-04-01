@extends('layouts.admin')
@section('title', 'Edit Aplikasi')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $siteApplication->title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.site-applications.update', $siteApplication) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.site-applications.partials.form', ['siteApplication' => $siteApplication])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.site-applications.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
