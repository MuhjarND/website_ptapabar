@extends('layouts.admin')
@section('title', 'Link Cepat')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Link Cepat / Layanan</h2>
        <a href="{{ route('admin.quicklinks.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Link</a>
    </div>
    <div class="card-body">
        <table>
            <thead><tr><th>Ikon</th><th>Judul</th><th>Deskripsi</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($links as $link)
                <tr>
                    <td>
                        @if($link->icon)
                            <img src="{{ asset('storage/'.$link->icon) }}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;">
                        @else
                            <i class="fas fa-link" style="font-size:24px;color:#999;"></i>
                        @endif
                    </td>
                    <td><strong>{{ $link->title }}</strong></td>
                    <td style="max-width:200px;font-size:12px;color:#666;">{{ Str::limit($link->description, 60) }}</td>
                    <td>{{ $link->order }}</td>
                    <td>{!! $link->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.quicklinks.edit', $link) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.quicklinks.destroy', $link) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada link cepat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
