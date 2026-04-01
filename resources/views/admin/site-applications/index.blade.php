@extends('layouts.admin')
@section('title', 'Aplikasi')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Aplikasi</h2>
        <a href="{{ route('admin.site-applications.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Aplikasi</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Ikon</th>
                    <th>Judul</th>
                    <th>Grup</th>
                    <th>Deskripsi</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td>
                            @if($application->icon)
                                <img src="{{ asset('storage/' . $application->icon) }}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;">
                            @else
                                <i class="fas fa-th-large" style="font-size:24px;color:#999;"></i>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $application->title }}</strong>
                            @if($application->url)
                                <div><a href="{{ $application->url }}" target="_blank" style="font-size:12px;color:#0d5c2f;">{{ \Illuminate\Support\Str::limit($application->url, 40) }}</a></div>
                            @endif
                        </td>
                        <td>{{ $groupOptions[$application->group_type] ?? $application->group_type }}</td>
                        <td style="max-width:220px;font-size:12px;color:#666;">{{ \Illuminate\Support\Str::limit($application->description, 70) }}</td>
                        <td>{{ $application->order }}</td>
                        <td>{!! $application->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.site-applications.edit', $application) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.site-applications.destroy', $application) }}" onsubmit="return confirm('Hapus aplikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#999;padding:32px;">Belum ada aplikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $applications->links() }}</div>
@endsection
