<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BoksController;
use App\Http\Controllers\ImportSuratKeluarController;
use App\Http\Controllers\ImportSuratMasukController;
use App\Http\Controllers\PengodeanController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\ItemBerkasController;
use App\Http\Controllers\PemindahanController;
use App\Http\Controllers\PenyusutanController;
use App\Http\Controllers\JejakAuditController;
use App\Http\Controllers\SaranKlasifikasiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\JenisNaskahController;
use App\Http\Controllers\UnitPengolahController;
use App\Http\Controllers\KlasifikasiController;

// ===== Tanpa perlu masuk =====
Route::get('/masuk', [AuthController::class, 'form'])->name('login')->middleware('guest');
Route::post('/masuk', [AuthController::class, 'masuk'])->name('login.masuk')->middleware('guest');
Route::post('/keluar', [AuthController::class, 'keluar'])->name('keluar')->middleware('auth');

Route::get('/', fn () => redirect()->route('beranda'));

// ===== Wajib masuk =====
Route::middleware('auth')->group(function () {

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/klasifikasi/cari', [SuratMasukController::class, 'cariKlasifikasi'])->name('klasifikasi.cari');
    Route::get('/arsip/{arsip}/saran-klasifikasi', [SaranKlasifikasiController::class, 'untukArsip'])->name('arsip.saran-klasifikasi');

    // ---------- SURAT MASUK ----------
    // Hanya Sekretariat dan Unit Kearsipan.
    // Route berkata tetap harus di atas route {suratMasuk}.
    Route::middleware('can:akses-surat-masuk')->group(function () {
        Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
        Route::get('/surat-masuk/tambah', [SuratMasukController::class, 'create'])->name('surat-masuk.create');
        Route::post('/surat-masuk/simpan', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
        Route::get('/surat-masuk/import', [ImportSuratMasukController::class, 'form'])->name('surat-masuk.import');
        Route::post('/surat-masuk/import', [ImportSuratMasukController::class, 'proses'])->name('surat-masuk.import.proses');
        Route::get('/surat-masuk/import-dokumen', [ImportSuratMasukController::class, 'formDokumen'])->name('surat-masuk.import-dokumen');
        Route::post('/surat-masuk/import-dokumen', [ImportSuratMasukController::class, 'prosesDokumen'])->name('surat-masuk.import-dokumen.proses');
        Route::get('/surat-masuk/pengodean', [PengodeanController::class, 'index'])->name('surat-masuk.pengodean');
        Route::patch('/surat-masuk/pengodean/{arsip}', [PengodeanController::class, 'simpan'])->name('surat-masuk.pengodean.simpan');

        Route::get('/surat-masuk/cetak', [SuratMasukController::class, 'cetak'])->name('surat-masuk.cetak');
        Route::get('/surat-masuk/ekspor-excel', [SuratMasukController::class, 'eksporExcel'])->name('surat-masuk.ekspor-excel');

        Route::get('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');
        Route::get('/surat-masuk/{suratMasuk}/edit', [SuratMasukController::class, 'edit'])->name('surat-masuk.edit');
        Route::put('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
        Route::delete('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
        Route::post('/surat-masuk/{suratMasuk}/dokumen', [SuratMasukController::class, 'unggahDokumen'])->name('surat-masuk.dokumen');
    });

    // ---------- SURAT KELUAR ----------
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('/surat-keluar/tambah', [SuratKeluarController::class, 'create'])->name('surat-keluar.create');
    Route::post('/surat-keluar/simpan', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('/surat-keluar/usul-nomor', [SuratKeluarController::class, 'usulNomor'])->name('surat-keluar.usul-nomor');
    Route::get('/surat-keluar/import', [ImportSuratKeluarController::class, 'form'])->name('surat-keluar.import');
    Route::post('/surat-keluar/import', [ImportSuratKeluarController::class, 'proses'])->name('surat-keluar.import.proses');
    Route::get('/surat-keluar/import-dokumen', [ImportSuratKeluarController::class, 'formDokumen'])->name('surat-keluar.import-dokumen');
    Route::post('/surat-keluar/import-dokumen', [ImportSuratKeluarController::class, 'prosesDokumen'])->name('surat-keluar.import-dokumen.proses');

    Route::get('/surat-keluar/cetak', [SuratKeluarController::class, 'cetak'])->name('surat-keluar.cetak');
    Route::get('/surat-keluar/ekspor-excel', [SuratKeluarController::class, 'eksporExcel'])->name('surat-keluar.ekspor-excel');

    Route::get('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
    Route::get('/surat-keluar/{suratKeluar}/edit', [SuratKeluarController::class, 'edit'])->name('surat-keluar.edit');
    Route::put('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    Route::post('/surat-keluar/{suratKeluar}/dokumen', [SuratKeluarController::class, 'unggahDokumen'])->name('surat-keluar.dokumen');

    // ---------- MASTER (superadmin) ----------
    // Route bernama tetap harus di atas route {kelompok}, yang menangkap apa pun.
    Route::middleware('can:kelola-master')->group(function () {
        Route::get('/master/jenis-naskah', [JenisNaskahController::class, 'index'])->name('jenis-naskah.index');
        Route::get('/master/jenis-naskah/tambah', [JenisNaskahController::class, 'create'])->name('jenis-naskah.create');
        Route::post('/master/jenis-naskah', [JenisNaskahController::class, 'store'])->name('jenis-naskah.store');
        Route::get('/master/jenis-naskah/{jenisNaskah}/edit', [JenisNaskahController::class, 'edit'])->name('jenis-naskah.edit');
        Route::put('/master/jenis-naskah/{jenisNaskah}', [JenisNaskahController::class, 'update'])->name('jenis-naskah.update');
        Route::delete('/master/jenis-naskah/{jenisNaskah}', [JenisNaskahController::class, 'destroy'])->name('jenis-naskah.destroy');

        Route::get('/master/unit-pengolah', [UnitPengolahController::class, 'index'])->name('unit-pengolah.index');
        Route::get('/master/unit-pengolah/tambah', [UnitPengolahController::class, 'create'])->name('unit-pengolah.create');
        Route::post('/master/unit-pengolah', [UnitPengolahController::class, 'store'])->name('unit-pengolah.store');
         Route::get('/master/unit-pengolah/{kode}/edit', [UnitPengolahController::class, 'edit'])
            ->where('kode', '.*')->name('unit-pengolah.edit');
        Route::put('/master/unit-pengolah/{kode}', [UnitPengolahController::class, 'update'])
            ->where('kode', '.*')->name('unit-pengolah.update');
        Route::delete('/master/unit-pengolah/{kode}', [UnitPengolahController::class, 'destroy'])
            ->where('kode', '.*')->name('unit-pengolah.destroy');
        Route::get('/master/klasifikasi', [KlasifikasiController::class, 'index'])->name('klasifikasi.index');
        Route::get('/master/klasifikasi/tambah', [KlasifikasiController::class, 'create'])->name('klasifikasi.create');
        Route::post('/master/klasifikasi', [KlasifikasiController::class, 'store'])->name('klasifikasi.store');
        Route::get('/master/klasifikasi/{kode}/edit', [KlasifikasiController::class, 'edit'])
            ->where('kode', '.*')->name('klasifikasi.edit');
        Route::put('/master/klasifikasi/{kode}', [KlasifikasiController::class, 'update'])
            ->where('kode', '.*')->name('klasifikasi.update');
            Route::get('/master/{kelompok}', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/master/{kelompok}', [PengaturanController::class, 'store'])->name('pengaturan.store');
        Route::delete('/master/{kelompok}/{pengaturan}', [PengaturanController::class, 'destroy'])->name('pengaturan.destroy');
    });

    // ---------- KELOLA AKUN (superadmin) ----------
    Route::middleware(['auth', 'can:kelola-akun'])->group(function () {
        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::get('/akun/tambah', [AkunController::class, 'create'])->name('akun.create');
        Route::post('/akun/simpan', [AkunController::class, 'store'])->name('akun.store');
        Route::get('/akun/{akun}/edit', [AkunController::class, 'edit'])->name('akun.edit');
        Route::put('/akun/{akun}', [AkunController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{akun}', [AkunController::class, 'destroy'])->name('akun.destroy');
    });

    // ---------- BOKS ----------
    Route::get('/boks', [BoksController::class, 'index'])->name('boks.index');
    Route::get('/boks/tambah', [BoksController::class, 'create'])->name('boks.create');
    Route::post('/boks/simpan', [BoksController::class, 'store'])->name('boks.store');
    Route::get('/boks/cetak/label', [BoksController::class, 'cetakLabel'])->name('boks.cetak-label');
    Route::get('/boks/{bok}', [BoksController::class, 'show'])->name('boks.show');
    Route::get('/boks/{bok}/edit', [BoksController::class, 'edit'])->name('boks.edit');
    Route::put('/boks/{bok}', [BoksController::class, 'update'])->name('boks.update');
    Route::delete('/boks/{bok}', [BoksController::class, 'destroy'])->name('boks.destroy');

    // ---------- BERKAS ----------
    Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas.index');
    Route::get('/berkas/tambah', [BerkasController::class, 'create'])->name('berkas.create');
    Route::post('/berkas/simpan', [BerkasController::class, 'store'])->name('berkas.store');
    Route::get('/berkas/cetak/daftar', [BerkasController::class, 'cetakDaftar'])->name('berkas.cetak-daftar');
    Route::get('/berkas/ekspor-daftar-excel', [BerkasController::class, 'eksporDaftarExcel'])->name('berkas.ekspor-daftar-excel');

    Route::get('/berkas/{berka}/cetak', [BerkasController::class, 'cetakIsi'])->name('berkas.cetak-isi');
    Route::get('/berkas/{berka}/ekspor-isi-excel', [BerkasController::class, 'eksporIsiExcel'])->name('berkas.ekspor-isi-excel');
    Route::get('/berkas/{berka}', [BerkasController::class, 'show'])->name('berkas.show');
    Route::get('/berkas/{berka}/edit', [BerkasController::class, 'edit'])->name('berkas.edit');
    Route::put('/berkas/{berka}', [BerkasController::class, 'update'])->name('berkas.update');
    Route::delete('/berkas/{berka}', [BerkasController::class, 'destroy'])->name('berkas.destroy');
    Route::post('/berkas/{berka}/ajukan', [BerkasController::class, 'ajukan'])->name('berkas.ajukan');
    Route::post('/berkas/{berka}/verifikasi', [BerkasController::class, 'verifikasi'])->name('berkas.verifikasi');
    Route::post('/berkas/{berka}/buka-kunci', [BerkasController::class, 'bukaKunci'])->name('berkas.buka-kunci');

    // ---------- ITEM BERKAS ----------
    Route::get('/berkas/{berka}/item/pilih', [ItemBerkasController::class, 'pilih'])->name('item-berkas.pilih');
    Route::post('/berkas/{berka}/item/dari-arsip', [ItemBerkasController::class, 'tambahDariArsip'])->name('item-berkas.dari-arsip');
    Route::get('/berkas/{berka}/item/tambah', [ItemBerkasController::class, 'create'])->name('item-berkas.create');
    Route::post('/berkas/{berka}/item/simpan', [ItemBerkasController::class, 'store'])->name('item-berkas.store');
    Route::get('/berkas/{berka}/item/{item}/edit', [ItemBerkasController::class, 'edit'])->name('item-berkas.edit');
    Route::put('/berkas/{berka}/item/{item}', [ItemBerkasController::class, 'update'])->name('item-berkas.update');
    Route::delete('/berkas/{berka}/item/{item}', [ItemBerkasController::class, 'destroy'])->name('item-berkas.destroy');

    // ---------- KEPEGAWAIAN ----------
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/tambah', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai/simpan', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
    Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // ---------- KEUANGAN ----------
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');

    // ---------- PEMINDAHAN ARSIP INAKTIF ----------
    Route::get('/pemindahan', [PemindahanController::class, 'index'])->name('pemindahan.index');
    Route::get('/pemindahan/ajukan', [PemindahanController::class, 'create'])->name('pemindahan.create');
    Route::post('/pemindahan/simpan', [PemindahanController::class, 'store'])->name('pemindahan.store');
    Route::get('/pemindahan/{pemindahan}', [PemindahanController::class, 'show'])->name('pemindahan.show');
    Route::get('/pemindahan/{pemindahan}/cetak-ba', [PemindahanController::class, 'cetakBa'])->name('pemindahan.cetak-ba');
    Route::post('/pemindahan/{pemindahan}/terima', [PemindahanController::class, 'terima'])->name('pemindahan.terima');
    Route::post('/pemindahan/{pemindahan}/tolak', [PemindahanController::class, 'tolak'])->name('pemindahan.tolak');

    // ---------- PENYUSUTAN AKHIR ----------
    Route::get('/penyusutan', [PenyusutanController::class, 'index'])->name('penyusutan.index');
    Route::get('/penyusutan/catat', [PenyusutanController::class, 'create'])->name('penyusutan.create');
    Route::post('/penyusutan/simpan', [PenyusutanController::class, 'store'])->name('penyusutan.store');
    Route::get('/penyusutan/{penyusutan}', [PenyusutanController::class, 'show'])->name('penyusutan.show');
    Route::get('/penyusutan/{penyusutan}/cetak-ba', [PenyusutanController::class, 'cetakBa'])->name('penyusutan.cetak-ba');
    Route::delete('/penyusutan/{penyusutan}', [PenyusutanController::class, 'destroy'])->name('penyusutan.destroy');

    Route::get('/jejak', [JejakAuditController::class, 'index'])->name('jejak.index');
});