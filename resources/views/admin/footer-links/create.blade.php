@extends('layouts.admin')
@section('title', 'Tambah Tautan Footer')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Tautan Footer</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.footer-links.store') }}">
            @csrf
            @include('admin.footer-links.partials.form')
        </form>
    </div>
</div>
@endsection
