<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Kalau sudah login, langsung redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('documents.index');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        // Cek apakah user aktif
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update last_login_at
            Auth::user()->update(['last_login_at' => now()]);

            // Catat activity log
            ActivityLog::record(
                action: ActivityLog::ACTION_USER_LOGIN,
                userId: Auth::id(),
                description: 'Login berhasil: ' . Auth::user()->name,
            );

            return redirect()->intended(route('documents.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // Login gagal
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        // Catat activity log sebelum logout
        ActivityLog::record(
            action: ActivityLog::ACTION_USER_LOGOUT,
            userId: Auth::id(),
            description: 'Logout: ' . Auth::user()->name,
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
