@extends('layouts.admin')
@section('title', 'Ikon Mengambang')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Kelola Ikon Mengambang</h2>
        <a href="{{ route('admin.floating-icons.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Ikon</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Ikon</th>
                    <th>Judul</th>
                    <th>Teks</th>
                    <th>Link</th>
                    <th>Posisi</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($floatingIcons as $floatingIcon)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $floatingIcon->icon) }}" alt="{{ $floatingIcon->title }}" style="width:48px;height:48px;object-fit:contain;border-radius:12px;border:1px solid #dce8df;background:#f8fbf8;padding:4px;">
                        </td>
                        <td><strong>{{ $floatingIcon->title }}</strong></td>
                        <td>{{ $floatingIcon->text ?: '-' }}</td>
                        <td><a href="{{ $floatingIcon->url }}" target="_blank" rel="noopener" style="font-size:12px;color:#0d5c2f;">{{ \Illuminate\Support\Str::limit($floatingIcon->url, 52) }}</a></td>
                        <td>{{ \App\FloatingIcon::positionOptions()[$floatingIcon->position ?? \App\FloatingIcon::POSITION_BOTTOM_RIGHT] ?? 'Pojok Kanan Bawah' }}</td>
                        <td>{{ $floatingIcon->order }}</td>
                        <td>{!! $floatingIcon->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.floating-icons.edit', $floatingIcon) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.floating-icons.destroy', $floatingIcon) }}" onsubmit="return confirm('Hapus ikon mengambang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#999;padding:32px;">Belum ada ikon mengambang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $floatingIcons->links() }}</div>
@endsection
