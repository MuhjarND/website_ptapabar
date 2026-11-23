@extends('layouts.admin')
@section('title', 'Tambah Agenda Pimpinan')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Agenda Pimpinan</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.agendas.store') }}">
            @csrf
            @include('admin.agendas.partials.form')
        </form>
    </div>
</div>
@endsection
