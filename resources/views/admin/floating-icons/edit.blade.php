@extends('layouts.admin')
@section('title', 'Edit Ikon Mengambang')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit Ikon Mengambang</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.floating-icons.update', $floatingIcon) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.floating-icons.partials.form', ['floatingIcon' => $floatingIcon])
        </form>
    </div>
</div>
@endsection
