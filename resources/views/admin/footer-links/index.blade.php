@extends('layouts.admin')
@section('title', 'Tautan Footer')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Kelola Tautan Footer</h2>
        <a href="{{ route('admin.footer-links.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Tautan</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Bagian</th>
                    <th>Judul</th>
                    <th>URL</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                    <tr>
                        <td>{{ $link->section_label }}</td>
                        <td><strong>{{ $link->title }}</strong></td>
                        <td><a href="{{ $link->url }}" target="_blank" rel="noopener" style="font-size:12px;color:#0d5c2f;">{{ \Illuminate\Support\Str::limit($link->url, 60) }}</a></td>
                        <td>{{ $link->order }}</td>
                        <td>{!! $link->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.footer-links.edit', $link) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.footer-links.destroy', $link) }}" onsubmit="return confirm('Hapus tautan footer ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada tautan footer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
