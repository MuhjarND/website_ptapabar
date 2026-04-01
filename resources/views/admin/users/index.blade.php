@extends('layouts.admin')
@section('title', 'User')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Master Data User</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah User</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Post</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === \App\User::ROLE_ADMIN ? 'badge-warning' : 'badge-info' }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td>{{ $user->posts_count }}</td>
                        <td class="text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $users->links() }}</div>
@endsection
