@extends('layouts.admin')
@section('title', 'Zona Integritas')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Eviden Zona Integritas</h2>
        <a href="{{ route('admin.integrity-zones.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Eviden</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Area</th>
                    <th>Link Eviden</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($integrityZones as $integrityZone)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $integrityZone->image) }}" alt="{{ $integrityZone->title }}" style="width:68px;height:88px;object-fit:cover;border-radius:10px;border:1px solid #dce8df;">
                        </td>
                        <td><strong>{{ $integrityZone->title }}</strong></td>
                        <td>
                            <a href="{{ $integrityZone->url }}" target="_blank" rel="noopener" style="font-size:12px;color:#0d5c2f;">
                                {{ \Illuminate\Support\Str::limit($integrityZone->url, 52) }}
                            </a>
                        </td>
                        <td>{{ $integrityZone->order }}</td>
                        <td>{!! $integrityZone->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.integrity-zones.edit', $integrityZone) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.integrity-zones.destroy', $integrityZone) }}" onsubmit="return confirm('Hapus eviden zona integritas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada eviden zona integritas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $integrityZones->links() }}</div>
@endsection
