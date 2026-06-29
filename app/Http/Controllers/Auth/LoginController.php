<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Tampilkan halaman login
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Proses login (username + password)
    public function login(Request $request)
    {
        $kredensial = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $ingat = $request->boolean('remember');

        // hanya akun berstatus 'aktif' yang boleh masuk
        $cek = [
            'username' => $kredensial['username'],
            'password' => $kredensial['password'],
            'status'   => 'aktif',
        ];

        if (Auth::attempt($cek, $ingat)) {
            $request->session()->regenerate();
            AuditLog::log('login', 'User', Auth::id(), 'Login: ' . $kredensial['username']);
            return redirect()->intended(route('dashboard'));
        }

        // Log percobaan login gagal
        AuditLog::log('login_gagal', 'User', null, 'Login gagal: ' . $kredensial['username']);

        throw ValidationException::withMessages([
            'username' => 'Username atau kata sandi salah, atau akun tidak aktif.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        AuditLog::log('logout', 'User', Auth::id(), 'Logout: ' . optional(Auth::user())->username);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
