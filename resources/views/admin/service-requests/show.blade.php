@extends('layouts.admin')
@section('title', 'Detail Layanan Publik')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Detail Layanan Publik</h2>
        <a href="{{ route('admin.service-requests.index') }}" class="btn" style="background:#e9ecef;">Kembali</a>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:20px;">
            <div class="stat-card" style="padding:20px;min-width:220px;">
                <div class="stat-label">Jenis Layanan</div>
                <div class="stat-number" style="font-size:20px;">{{ $serviceRequest->request_type_label }}</div>
            </div>
            <div class="stat-card" style="padding:20px;min-width:220px;">
                <div class="stat-label">Status</div>
                <div class="stat-number" style="font-size:20px;">{{ $serviceRequest->status_label }}</div>
            </div>
        </div>

        <table>
            <tbody>
                <tr><th style="width:220px;">Nama</th><td>{{ $serviceRequest->name }}</td></tr>
                <tr><th>Email</th><td>{{ $serviceRequest->email }}</td></tr>
                <tr><th>Telepon</th><td>{{ $serviceRequest->phone ?: '-' }}</td></tr>
                <tr><th>Subjek</th><td>{{ $serviceRequest->subject }}</td></tr>
                <tr><th>Pesan</th><td style="white-space:pre-line;">{{ $serviceRequest->message }}</td></tr>
                <tr><th>Lampiran</th><td>
                    @if($serviceRequest->attachment)
                        <a href="{{ route('admin.service-requests.download', $serviceRequest) }}" class="btn btn-sm btn-primary"><i class="fas fa-paperclip"></i> Unduh Lampiran</a>
                    @else
                        -
                    @endif
                </td></tr>
                <tr><th>Email Terkirim</th><td>{{ $serviceRequest->emailed_to ?: '-' }} @if($serviceRequest->emailed_at) <span class="text-muted">({{ $serviceRequest->emailed_at->format('d M Y H:i') }})</span> @endif</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>Tindak Lanjut Admin</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.service-requests.update', $serviceRequest) }}">
            @csrf
            @method('PUT')
            <div class="form-group" style="max-width:320px;">
                <label>Status</label>
                <select name="status" class="form-control">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $serviceRequest->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Catatan Admin</label>
                <textarea name="admin_notes" class="form-control" rows="6">{{ old('admin_notes', $serviceRequest->admin_notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Tindak Lanjut</button>
        </form>
    </div>
</div>
@endsection
