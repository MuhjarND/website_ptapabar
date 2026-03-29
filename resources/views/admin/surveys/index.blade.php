@extends('layouts.admin')
@section('title', 'Survei & Indeks')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Survei & Indeks Pelayanan Publik</h2>
        <a href="{{ route('admin.surveys.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Indeks</a>
    </div>
    <div class="card-body">
        <table>
            <thead><tr><th>Judul</th><th>Nilai</th><th>Label</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($surveys as $s)
                <tr>
                    <td><i class="{{ $s->icon }}" style="margin-right:8px;color:#c8a951;"></i>{{ $s->title }}</td>
                    <td><strong>{{ $s->value }}</strong></td>
                    <td><span class="badge badge-success">{{ $s->label }}</span></td>
                    <td>{{ $s->order }}</td>
                    <td>{!! $s->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.surveys.edit', $s) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.surveys.destroy', $s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada data indeks.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
