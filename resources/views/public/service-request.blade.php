@extends('layouts.public')
@section('title', 'Layanan Publik')

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="section section-white">
    <div class="container">
        <div class="content-shell">
            <div class="section-header">
                <h1 class="section-title">Aspirasi & Permohonan Informasi</h1>
            </div>
            <div class="card" style="background:#fff;border-radius:24px;border:1px solid var(--border);box-shadow:0 20px 50px rgba(13,92,47,0.08);">
                <div class="card-body" style="padding:32px;">
                    @if(session('success'))
                        <div class="alert alert-success" style="margin-bottom:20px;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert" style="margin-bottom:20px;background:#fff3f3;color:#a33;border:1px solid rgba(163,51,51,0.15);">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('service-request.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex gap-2" style="flex-wrap:wrap;">
                            <div class="form-group" style="min-width:220px;flex:1;">
                                <label>Jenis Layanan</label>
                                <select name="request_type" class="form-control">
                                    @foreach($requestTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('request_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="min-width:220px;flex:1;">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="d-flex gap-2" style="flex-wrap:wrap;">
                            <div class="form-group" style="min-width:220px;flex:1;">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group" style="min-width:220px;flex:1;">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Subjek</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Pesan / Uraian</label>
                            <textarea name="message" class="form-control" rows="7" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Lampiran Pendukung (opsional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format PDF/JPG/PNG, maksimal 5MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Permintaan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
