@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit User: {{ $user->name }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users.partials.form', ['userModel' => $user])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.users.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
