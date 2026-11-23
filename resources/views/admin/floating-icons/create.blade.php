@extends('layouts.admin')
@section('title', 'Tambah Ikon Mengambang')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Ikon Mengambang</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.floating-icons.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.floating-icons.partials.form')
        </form>
    </div>
</div>
@endsection
