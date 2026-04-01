@extends('layouts.admin')
@section('title', 'Tambah User')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah User</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.partials.form', ['userModel' => null])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
