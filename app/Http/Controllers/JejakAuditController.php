<?php

namespace App\Http\Controllers;

use App\Models\JejakAudit;
use App\Models\User;
use Illuminate\Http\Request;

class JejakAuditController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->lihatSemuaUnit(),
            403,
            'Riwayat perubahan hanya dapat dilihat oleh Unit Kearsipan dan Superadmin.'
        );

        $jejak = JejakAudit::with('user')
            ->when($request->filled('model'), fn ($q) => $q->where('model', $request->model))
            ->when($request->filled('aksi'), fn ($q) => $q->where('aksi', $request->aksi))
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->user))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->orderByDesc('created_at')
            ->paginate(50)->withQueryString();

        return view('jejak.index', [
            'jejak'       => $jejak,
            'daftarModel' => JejakAudit::distinct()->orderBy('model')->pluck('model'),
            'daftarUser'  => User::orderBy('name')->get(),
        ]);
    }
}