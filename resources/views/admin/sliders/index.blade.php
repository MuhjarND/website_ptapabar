@extends('layouts.admin')
@section('title', 'Kelola Slider')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Slider</h2>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Slider</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>Preview</th><th>Judul</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($sliders as $slider)
                <tr>
                    <td><img src="{{ asset('storage/'.$slider->image) }}" style="height:50px;border-radius:6px;object-fit:cover"></td>
                    <td>{{ $slider->title ?? '-' }}</td>
                    <td>{{ $slider->order }}</td>
                    <td><span class="badge {{ $slider->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $slider->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" onsubmit="return confirm('Hapus slider?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:#999;padding:32px;">Belum ada slider.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
