@extends('layouts.admin')
@php($title = $type === 'hearings' ? 'Jadwal Sidang Banding' : 'Statistik Perkara')
@section('title', $title)
@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ $title }}</h2>
        <a class="btn btn-gold" href="{{ route('admin.court-information.create', $type) }}">Tambah Data</a>
    </div>
    <div class="card-body" style="overflow-x:auto">
        <table>
            <thead><tr><th>Periode</th><th>Informasi</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $type === 'hearings' ? $row->hearing_date->format('d-m-Y') : $row->period->format('m-Y') }}</td>
                    <td>{{ $type === 'hearings' ? $row->title : $row->source }}</td>
                    <td>{{ $row->is_published ? 'Terbit' : 'Draf' }}</td>
                    <td><a class="btn btn-sm btn-primary" href="{{ route('admin.court-information.edit', [$type, $row->id]) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $rows->links() }}</div>
    </div>
</div>
@endsection
