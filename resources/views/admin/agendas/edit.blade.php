@extends('layouts.admin')
@section('title', 'Edit Agenda Pimpinan')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit Agenda Pimpinan</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.agendas.update', $agenda) }}">
            @csrf
            @method('PUT')
            @include('admin.agendas.partials.form')
        </form>
    </div>
</div>
@endsection
