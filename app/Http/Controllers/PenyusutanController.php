<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Penyusutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyusutanController extends Controller
{
    public function __construct()
    {
        // Penyusutan akhir sepenuhnya kewenangan Unit Kearsipan
    }

    public function index(Request $request)
    {
        $this->pastikanKearsipan();

        $penyusutan = Penyusutan::with('pencatat')
            ->withCount('berkas')
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        return view('penyusutan.index', compact('penyusutan'));
    }

    // Memilih berkas yang sudah habis masa retensinya
    public function create(Request $request)
    {
        $this->pastikanKearsipan();

        $jenis = $request->input('jenis') === 'serah' ? 'serah' : 'musnah';

        // Nasib akhir menentukan jalur: Musnah atau Permanen
        $nasib = $jenis === 'musnah' ? 'Musnah' : 'Permanen';

        $kandidat = Berkas::with(['unit', 'klasifikasi', 'boks'])
            ->where('status', 'terverifikasi')
            ->whereNull('penyusutan_id')
            ->whereNotNull('kode_klasifikasi')
            ->whereHas('klasifikasi', fn ($q) => $q->where('nasib_akhir', 'like', "%{$nasib}%"))
            ->orderBy('unit_pengolah')->orderBy('tahun')->orderBy('no_berkas')
            ->get()
            ->filter(fn ($b) => str_starts_with($b->status_penyimpanan, 'Siap'));

        return view('penyusutan.create', compact('kandidat', 'jenis'));
    }

    public function store(Request $request)
    {
        $this->pastikanKearsipan();

        $jenis = $request->input('jenis') === 'serah' ? 'serah' : 'musnah';

        $aturan = [
            'jenis'          => ['required', 'in:musnah,serah'],
            'berkas_id'      => ['required', 'array', 'min:1'],
            'berkas_id.*'    => ['exists:berkas,id'],
            'nomor_ba'       => ['nullable', 'string', 'max:100'],
            'tanggal_ba'     => ['required', 'date'],
            'pihak1_nama'    => ['required', 'string', 'max:255'],
            'pihak1_nip'     => ['nullable', 'string', 'max:30'],
            'pihak1_pangkat' => ['nullable', 'string', 'max:255'],
            'pihak1_jabatan' => ['required', 'string', 'max:255'],
            'catatan'        => ['nullable', 'string'],
        ];

        if ($jenis === 'musnah') {
            // Pemusnahan wajib disaksikan dua orang
            $aturan += [
                'tempat'         => ['required', 'string', 'max:255'],
                'cara'           => ['required', 'string', 'max:100'],
                'saksi1_nama'    => ['required', 'string', 'max:255'],
                'saksi1_nip'     => ['nullable', 'string', 'max:30'],
                'saksi1_jabatan' => ['required', 'string', 'max:255'],
                'saksi2_nama'    => ['required', 'string', 'max:255'],
                'saksi2_nip'     => ['nullable', 'string', 'max:30'],
                'saksi2_jabatan' => ['required', 'string', 'max:255'],
            ];
        } else {
            $aturan += [
                'pihak2_nama'     => ['required', 'string', 'max:255'],
                'pihak2_nip'      => ['nullable', 'string', 'max:30'],
                'pihak2_pangkat'  => ['nullable', 'string', 'max:255'],
                'pihak2_jabatan'  => ['required', 'string', 'max:255'],
                'pihak2_instansi' => ['required', 'string', 'max:255'],
            ];
        }

        $data = $request->validate($aturan, [
            'berkas_id.required'      => 'Pilih minimal satu berkas.',
            'tanggal_ba.required'     => 'Tanggal pelaksanaan wajib diisi.',
            'saksi1_nama.required'    => 'Saksi pertama wajib diisi.',
            'saksi2_nama.required'    => 'Saksi kedua wajib diisi.',
            'tempat.required'         => 'Tempat pemusnahan wajib diisi.',
            'cara.required'           => 'Cara pemusnahan wajib dipilih.',
            'pihak2_instansi.required'=> 'Instansi penerima wajib diisi.',
        ]);

        $penyusutan = DB::transaction(function () use ($data) {
            $berkasId = $data['berkas_id'];
            unset($data['berkas_id']);

            $data['dicatat_oleh'] = auth()->id();

            $penyusutan = Penyusutan::create($data);

            Berkas::whereIn('id', $berkasId)
                ->whereNull('penyusutan_id')
                ->update([
                    'penyusutan_id'   => $penyusutan->id,
                    'disusutkan_pada' => $data['tanggal_ba'],
                ]);

            return $penyusutan;
        });

        return redirect()->route('penyusutan.show', $penyusutan)
            ->with('success', "{$penyusutan->label_jenis} dicatat untuk {$penyusutan->berkas()->count()} berkas.");
    }

    public function show(Penyusutan $penyusutan)
    {
        $this->pastikanKearsipan();

        $penyusutan->load(['berkas.unit', 'berkas.klasifikasi', 'berkas.boks', 'pencatat']);

        return view('penyusutan.show', compact('penyusutan'));
    }

    public function cetakBa(Penyusutan $penyusutan)
    {
        $this->pastikanKearsipan();

        $penyusutan->load(['berkas.unit', 'berkas.klasifikasi', 'berkas.boks']);

        return view('penyusutan.cetak_ba', compact('penyusutan'));
    }

    // Membatalkan pencatatan, misalnya bila salah input
    public function destroy(Penyusutan $penyusutan)
    {
        $this->pastikanKearsipan();

        DB::transaction(function () use ($penyusutan) {
            Berkas::where('penyusutan_id', $penyusutan->id)
                ->update(['penyusutan_id' => null, 'disusutkan_pada' => null]);

            $penyusutan->delete();
        });

        return redirect()->route('penyusutan.index')
            ->with('success', 'Pencatatan penyusutan dibatalkan. Berkas dikembalikan ke keadaan semula.');
    }

    private function pastikanKearsipan(): void
    {
        abort_unless(
            auth()->user()->lihatSemuaUnit(),
            403,
            'Penyusutan akhir hanya dapat dilakukan oleh Unit Kearsipan.'
        );
    }
}