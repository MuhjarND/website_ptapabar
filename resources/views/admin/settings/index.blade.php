@extends('layouts.admin')
@section('title', 'Pengaturan Situs')
@section('content')
<div class="card">
    <div class="card-header"><h2>Pengaturan Umum</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <div class="form-group">
                <label>Nama Instansi</label>
                <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] }}">
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="site_description" class="form-control" rows="3">{{ $settings['site_description'] }}</textarea>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" class="form-control" rows="2">{{ $settings['address'] }}</textarea>
            </div>
            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ $settings['phone'] }}" style="max-width:300px">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $settings['email'] }}" style="max-width:300px">
            </div>
            <div class="form-group">
                <label>Fax</label>
                <input type="text" name="fax" class="form-control" value="{{ $settings['fax'] }}" style="max-width:300px">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
        </form>
    </div>
</div>
@endsection
