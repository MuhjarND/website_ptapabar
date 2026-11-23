@extends('layouts.admin')
@section('title', 'Aspirasi & Informasi')
@section('content')
<div class="card">
    <div class="card-header"><h2>Inbox Aspirasi & Permohonan Informasi</h2></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.service-requests.index') }}" class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:20px;">
            <div class="form-group mb-0" style="min-width:220px;">
                <label>Jenis Layanan</label>
                <select name="request_type" class="form-control">
                    <option value="">Semua jenis</option>
                    @foreach($requestTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('request_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="min-width:220px;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>Pengirim</th>
                    <th>Jenis</th>
                    <th>Subjek</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceRequests as $serviceRequest)
                    <tr>
                        <td><strong>{{ $serviceRequest->name }}</strong></td>
                        <td><span class="badge badge-info">{{ $serviceRequest->request_type_label }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($serviceRequest->subject, 60) }}</td>
                        <td>
                            <span class="badge {{ $serviceRequest->status === \App\ServiceRequest::STATUS_SELESAI ? 'badge-success' : ($serviceRequest->status === \App\ServiceRequest::STATUS_DITINDAKLANJUTI ? 'badge-warning' : 'badge-secondary') }}">
                                {{ $serviceRequest->status_label }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $serviceRequest->email }}</td>
                        <td class="text-muted">{{ $serviceRequest->created_at->format('d M Y H:i') }}</td>
                        <td><a href="{{ route('admin.service-requests.show', $serviceRequest) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#999;padding:32px;">Belum ada aspirasi atau permohonan informasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $serviceRequests->appends(request()->query())->links() }}</div>
@endsection
