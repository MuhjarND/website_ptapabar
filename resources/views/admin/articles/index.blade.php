@extends('layouts.admin')
@section('title', 'Artikel')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Kelola Artikel</h2>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Artikel</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Ringkasan</th>
                    <th>Sumber PDF</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td>
                            <strong>{{ $article->title }}</strong><br>
                            <span class="text-muted">{{ $article->slug }}</span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $article->article_category_label ?: 'Lain-lain' }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($article->excerpt_plain, 80) }}</td>
                        <td>
                            @if($article->pdf_file)
                                <span class="badge badge-info">Upload PDF</span>
                            @elseif($article->pdf_url)
                                <span class="badge badge-warning">Link PDF</span>
                            @else
                                <span class="badge badge-secondary">Halaman Artikel</span>
                            @endif
                        </td>
                        <td>{{ $article->order }}</td>
                        <td>{!! $article->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' !!}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ $article->target_url }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#999;padding:32px;">Belum ada artikel.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $articles->links() }}</div>
@endsection
