@extends('layouts.admin')
@section('title', 'Foto & Video')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Foto & Video</h2>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Item</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($galleries as $item)
                <tr>
                    <td>
                        @if($item->type === 'video' && $item->youtube_id)
                            <div style="position:relative;width:80px;height:50px;border-radius:6px;overflow:hidden;">
                                <img src="https://img.youtube.com/vi/{{ $item->youtube_id }}/mqdefault.jpg" style="width:100%;height:100%;object-fit:cover;">
                                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);">
                                    <i class="fas fa-play-circle" style="color:#fff;font-size:20px;"></i>
                                </div>
                            </div>
                        @elseif($item->file)
                            <img src="{{ asset('storage/'.$item->file) }}" style="height:50px;border-radius:6px;object-fit:cover;">
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($item->title, 40) }}</td>
                    <td><span class="badge {{ $item->type === 'video' ? 'badge-danger' : 'badge-info' }}">{{ $item->type === 'video' ? 'Video' : 'Foto' }}</span></td>
                    <td>{{ $item->order }}</td>
                    <td><span class="badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.galleries.edit', $item) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.galleries.destroy', $item) }}" onsubmit="return confirm('Hapus item galeri ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada item galeri.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $galleries->links() }}</div>
@endsection
