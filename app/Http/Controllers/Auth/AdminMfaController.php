<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class AdminMfaController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403);

        return response()->view('auth.admin-mfa', ['enrolled' => (bool) $user->adminMfaVersion()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function verify(Request $request, Google2FA $authenticator)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() && $user->adminMfaVersion(), 403);
        $code = $request->input('otp_code');
        $step = is_string($code) && preg_match('/\A[0-9]{6}\z/', $code)
            ? $authenticator->verifyKeyNewer($user->admin_mfa_secret, $code, $user->admin_mfa_step ?? 0, 1)
            : false;

        if ($step === false || !User::whereKey($user->id)->where('role', User::ROLE_ADMIN)
            ->where('admin_mfa_secret', $user->getRawOriginal('admin_mfa_secret'))
            ->where(fn ($query) => $query->whereNull('admin_mfa_step')->orWhere('admin_mfa_step', '<', $step))
            ->update(['admin_mfa_step' => $step])) {
            return redirect()->route('admin-mfa.show')->withErrors(['otp_code' => 'Kode tidak berlaku atau sudah dipakai. Tunggu kode baru.']);
        }

        $request->session()->regenerate(true);
        $request->session()->put('admin_mfa_version', $user->adminMfaVersion());

        return redirect()->route('admin.dashboard');
    }
}
