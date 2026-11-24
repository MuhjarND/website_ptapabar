@extends('layouts.public')
@section('title', 'Verifikasi Admin')

@section('content')
<div class="section court-section">
    <div class="container">
        <section class="page-content" aria-labelledby="mfa-title">
            <h1 id="mfa-title">Verifikasi Admin</h1>
            @if($enrolled)
                <p>Masukkan kode 6 digit dari aplikasi autentikator.</p>
                @error('otp_code')<p role="alert">{{ $message }}</p>@enderror
                <form class="court-filter" method="post" action="{{ route('admin-mfa.verify') }}">
                    @csrf
                    <label for="otp-code">Kode verifikasi</label>
                    <input id="otp-code" name="otp_code" type="text" inputmode="numeric" autocomplete="one-time-code"
                        pattern="[0-9]{6}" maxlength="6" required>
                    <button type="submit">Verifikasi</button>
                </form>
            @else
                <p>Akun ini belum didaftarkan ke verifikasi dua langkah. Hubungi pengelola hosting untuk mendaftarkannya.</p>
            @endif
            <form class="court-filter" method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </section>
    </div>
</div>
@endsection
