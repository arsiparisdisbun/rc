<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function form()
    {
        return view('auth.login');
    }

    public function masuk(Request $request)
    {
        $kredensial = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'username' => 'nama pengguna',
            'password' => 'kata sandi',
        ]);

        // Akun nonaktif tidak bisa masuk meski kata sandinya benar
        $kredensial['aktif'] = true;

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            throw ValidationException::withMessages([
                'username' => 'Nama pengguna atau kata sandi salah, atau akun sedang nonaktif.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('beranda'));
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}