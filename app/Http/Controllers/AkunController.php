<?php

namespace App\Http\Controllers;

use App\Models\UnitPengolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    public function index()
    {
        return view('akun.index', [
            'akun' => User::with('unit')->orderBy('peran')->orderBy('username')->get(),
        ]);
    }

    public function create()
    {
        return view('akun.create', ['daftarUnit' => UnitPengolah::aktif()->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());

        // Hanya operator yang terikat pada satu unit
        if ($data['peran'] !== 'operator') {
            $data['unit_pengolah'] = null;
        }

        $data['aktif'] = $request->boolean('aktif', true);

        User::create($data);

        return redirect()->route('akun.index')->with('success', "Akun {$data['username']} berhasil dibuat.");
    }

    public function edit(User $akun)
    {
        return view('akun.edit', [
            'akun'       => $akun,
            'daftarUnit' => UnitPengolah::aktif()->get(),
        ]);
    }

    public function update(Request $request, User $akun)
    {
        $data = $request->validate($this->aturan($akun), $this->pesan());

        if ($data['peran'] !== 'operator') {
            $data['unit_pengolah'] = null;
        }

        // Kata sandi hanya diganti bila diisi
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['aktif'] = $request->boolean('aktif');

        // Superadmin tidak boleh menonaktifkan atau menurunkan dirinya sendiri
        if ($akun->id === auth()->id()) {
            $data['aktif'] = true;
            $data['peran'] = $akun->peran;
        }

        $akun->update($data);

        return redirect()->route('akun.index')->with('success', "Akun {$akun->username} berhasil diperbarui.");
    }

    public function destroy(User $akun)
    {
        abort_if($akun->id === auth()->id(), 403, 'Anda tidak dapat menghapus akun sendiri.');

        $nama = $akun->username;
        $akun->delete();

        return redirect()->route('akun.index')->with('success', "Akun {$nama} telah dihapus.");
    }

    private function aturan(?User $akun = null): array
    {
        return [
            'username'      => ['required', 'string', 'max:50', 'alpha_dash',
                                Rule::unique('users', 'username')->ignore($akun?->id)],
            'name'          => ['required', 'string', 'max:255'],
            'peran'         => ['required', 'in:operator,kearsipan,superadmin'],
            'unit_pengolah' => ['nullable', 'required_if:peran,operator', 'exists:unit_pengolah,kode'],
            'password'      => [$akun ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    private function pesan(): array
    {
        return [
            'username.alpha_dash'        => 'Nama pengguna hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'username.unique'            => 'Nama pengguna sudah dipakai akun lain.',
            'unit_pengolah.required_if'  => 'Operator wajib dikaitkan dengan satu unit pengolah.',
            'password.min'               => 'Kata sandi minimal 8 karakter.',
        ];
    }
}