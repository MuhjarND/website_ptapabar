@extends('layouts.admin')
@section('title', 'Edit Tautan Footer')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit Tautan Footer</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.footer-links.update', $footerLink) }}">
            @csrf
            @method('PUT')
            @include('admin.footer-links.partials.form', ['footerLink' => $footerLink])
        </form>
    </div>
</div>
@endsection
