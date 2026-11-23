@extends('layouts.admin')
@section('title', 'Agenda Pimpinan')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Agenda Pimpinan</h2>
        <a href="{{ route('admin.agendas.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Agenda Pimpinan</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.agendas.index') }}" class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:20px;">
            <div class="form-group mb-0" style="min-width:240px;">
                <label>Jenis Kegiatan</label>
                <select name="agenda_type" class="form-control">
                    <option value="">Semua jenis</option>
                    @foreach($agendaTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('agenda_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agendas as $agenda)
                    <tr>
                        <td>
                            <strong>{{ $agenda->title }}</strong>
                            @if($agenda->summary)
                                <div class="text-muted">{{ \Illuminate\Support\Str::limit($agenda->summary, 70) }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-info">{{ $agenda->agenda_type_label }}</span></td>
                        <td class="text-muted">{{ $agenda->start_at->format('d M Y H:i') }}</td>
                        <td class="text-muted">{{ $agenda->location ?: '-' }}</td>
                        <td>{!! $agenda->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.agendas.edit', $agenda) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.agendas.destroy', $agenda) }}" onsubmit="return confirm('Hapus agenda ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada agenda pimpinan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $agendas->appends(request()->query())->links() }}</div>
@endsection
