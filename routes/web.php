<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Halaman awal -> arahkan ke dashboard (atau login bila belum masuk)
Route::get('/', fn () => redirect()->route('dashboard'));

// ---------- Tamu (belum login) ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ---------- Sudah login ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifikasi/data',         [\App\Http\Controllers\NotifikasiController::class, 'data'])->name('notifikasi.data');
    Route::post('/notifikasi/baca-semua',  [\App\Http\Controllers\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::post('/notifikasi/{id}/baca',   [\App\Http\Controllers\NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    // ====== Contoh route khusus PENGURUS (RBAC) — diisi di langkah berikutnya ======
    Route::middleware('role:admin,ketua,sekretaris,bendahara')->group(function () {
        // Route::resource('warga', WargaController::class);
        // Route::resource('iuran', IuranController::class);
        // Route::resource('kas', KasController::class);
    });

    // ── Halaman Gang Saya (untuk koordinator yang login) ─────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('gang-saya',                  [\App\Http\Controllers\MyKoordinatorController::class, 'index'])->name('my-koordinator.index');
        Route::get('gang-saya/anggota',          [\App\Http\Controllers\MyKoordinatorController::class, 'anggotaList'])->name('my-koordinator.anggota.list');
        Route::get('gang-saya/kk-lookup',        [\App\Http\Controllers\MyKoordinatorController::class, 'kkLookup'])->name('my-koordinator.kk-lookup');
    });
    Route::middleware('auth')->group(function () {
        Route::post('gang-saya/anggota/add',     [\App\Http\Controllers\MyKoordinatorController::class, 'anggotaAdd'])->name('my-koordinator.anggota.add');
        Route::post('gang-saya/anggota/remove',  [\App\Http\Controllers\MyKoordinatorController::class, 'anggotaRemove'])->name('my-koordinator.anggota.remove');
    });

    // ── Master Koordinator Gang ──────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('koordinator',              [\App\Http\Controllers\KoordinatorGangController::class, 'index'])->name('koordinator.index');
        Route::get('koordinator/list',         [\App\Http\Controllers\KoordinatorGangController::class, 'list'])->name('koordinator.list');
        Route::get('koordinator/lookup',       [\App\Http\Controllers\KoordinatorGangController::class, 'lookup'])->name('koordinator.lookup');
        Route::get('koordinator/warga-lookup',                    [\App\Http\Controllers\KoordinatorGangController::class, 'wargaLookup'])->name('koordinator.warga-lookup');
        Route::get('koordinator/{koordinator}/anggota',           [\App\Http\Controllers\KoordinatorGangController::class, 'anggotaList'])->name('koordinator.anggota.list');
        Route::get('koordinator/{koordinator}/anggota-lookup',    [\App\Http\Controllers\KoordinatorGangController::class, 'anggotaKkLookup'])->name('koordinator.anggota.kk-lookup');
    });
    Route::middleware('role:admin,ketua,sekretaris')->group(function () {
        Route::post('koordinator/save',         [\App\Http\Controllers\KoordinatorGangController::class, 'save'])->name('koordinator.save');
        Route::post('koordinator/delete',       [\App\Http\Controllers\KoordinatorGangController::class, 'remove'])->name('koordinator.delete');
        Route::post('koordinator/anggota/add',  [\App\Http\Controllers\KoordinatorGangController::class, 'anggotaAdd'])->name('koordinator.anggota.add');
        Route::post('koordinator/anggota/remove',[\App\Http\Controllers\KoordinatorGangController::class, 'anggotaRemove'])->name('koordinator.anggota.remove');
    });

    // ── Keuangan: Gang ──────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('gang',                  [\App\Http\Controllers\GangController::class, 'index'])->name('gang.index');
        Route::get('gang/list',             [\App\Http\Controllers\GangController::class, 'list'])->name('gang.list');
        Route::get('gang/lookup',           [\App\Http\Controllers\GangController::class, 'lookup'])->name('gang.lookup');
    });
    Route::middleware('role:admin,ketua,sekretaris,bendahara')->group(function () {
        Route::post('gang/save',            [\App\Http\Controllers\GangController::class, 'save'])->name('gang.save');
        Route::post('gang/delete',          [\App\Http\Controllers\GangController::class, 'remove'])->name('gang.delete');
    });

    // ── Keuangan: Iuran ─────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('iuran',                    [\App\Http\Controllers\IuranController::class, 'index'])->name('iuran.index');
        Route::get('iuran/list',               [\App\Http\Controllers\IuranController::class, 'tagihanList'])->name('iuran.list');
        Route::get('iuran/context',            [\App\Http\Controllers\IuranController::class, 'context'])->name('iuran.context');
        Route::get('iuran/rekap',              [\App\Http\Controllers\IuranController::class, 'rekapBulanan'])->name('iuran.rekap');
        Route::get('iuran/tunggakan',          [\App\Http\Controllers\IuranController::class, 'tunggakan'])->name('iuran.tunggakan');
        Route::get('iuran/jenis/list',         [\App\Http\Controllers\IuranController::class, 'jenisList'])->name('iuran.jenis.list');
        Route::get('iuran/periode/list',       [\App\Http\Controllers\IuranController::class, 'periodeList'])->name('iuran.periode.list');
        Route::get('iuran/pembayaran/list',    [\App\Http\Controllers\IuranController::class, 'pembayaranList'])->name('iuran.pembayaran.list');
    });

    // ── Iuran Saya (warga: history tagihan sendiri) ──────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('iuran-saya',            [\App\Http\Controllers\IuranSayaController::class, 'index'])->name('iuran-saya.index');
        Route::get('iuran-saya/list',       [\App\Http\Controllers\IuranSayaController::class, 'list'])->name('iuran-saya.list');
        Route::get('iuran-saya/ringkasan',  [\App\Http\Controllers\IuranSayaController::class, 'ringkasan'])->name('iuran-saya.ringkasan');
        Route::get('iuran-saya/jenis',      [\App\Http\Controllers\IuranSayaController::class, 'jenisList'])->name('iuran-saya.jenis');
    });
    // Bayar & keringanan: koordinator juga bisa, otorisasi di controller (kkFilter)
    Route::middleware('auth')->group(function () {
        Route::post('iuran/pembayaran/save', [\App\Http\Controllers\IuranController::class, 'pembayaranSave'])->name('iuran.pembayaran.save');
        Route::post('iuran/keringanan',      [\App\Http\Controllers\IuranController::class, 'tandaiKeringanan'])->name('iuran.keringanan');
    });
    // Periode & manage: hanya admin/ketua/bendahara/sekretaris
    Route::middleware('role:admin,ketua,bendahara,sekretaris')->group(function () {
        Route::post('iuran/periode/buka',    [\App\Http\Controllers\IuranController::class, 'bukaPeriode'])->name('iuran.periode.buka');
        Route::post('iuran/periode/tutup',   [\App\Http\Controllers\IuranController::class, 'tutupBuku'])->name('iuran.periode.tutup');
        Route::post('iuran/import-tunggakan',[\App\Http\Controllers\IuranController::class, 'importTunggakan'])->name('iuran.import-tunggakan');
        Route::post('iuran/jenis/save',      [\App\Http\Controllers\IuranController::class, 'jenisSave'])->name('iuran.jenis.save');
        Route::post('iuran/jenis/delete',    [\App\Http\Controllers\IuranController::class, 'jenisRemove'])->name('iuran.jenis.delete');
    });

    // ── Fasilitas & Booking ──────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('fasilitas',                     [\App\Http\Controllers\FasilitasController::class, 'index'])->name('fasilitas.index');
        Route::get('fasilitas/list',                [\App\Http\Controllers\FasilitasController::class, 'list'])->name('fasilitas.list');
        Route::get('fasilitas/lookup',              [\App\Http\Controllers\FasilitasController::class, 'lookup'])->name('fasilitas.lookup');
        Route::get('fasilitas/tarif-lookup',        [\App\Http\Controllers\FasilitasController::class, 'tarifLookup'])->name('fasilitas.tarif-lookup');
        Route::get('fasilitas/tarif/{fasilitas}',   [\App\Http\Controllers\FasilitasController::class, 'tarifList'])->name('fasilitas.tarif.list');

        Route::get('booking',                       [\App\Http\Controllers\BookingController::class, 'index'])->name('booking.index');
        Route::get('booking/list',                  [\App\Http\Controllers\BookingController::class, 'list'])->name('booking.list');
        Route::get('booking/ringkasan',             [\App\Http\Controllers\BookingController::class, 'ringkasan'])->name('booking.ringkasan');
    });
    Route::middleware('role:admin,ketua,sekretaris,bendahara')->group(function () {
        Route::post('fasilitas/save',               [\App\Http\Controllers\FasilitasController::class, 'save'])->name('fasilitas.save');
        Route::post('fasilitas/delete',             [\App\Http\Controllers\FasilitasController::class, 'remove'])->name('fasilitas.delete');
        Route::post('fasilitas/tarif/save',         [\App\Http\Controllers\FasilitasController::class, 'tarifSave'])->name('fasilitas.tarif.save');
        Route::post('fasilitas/tarif/delete',       [\App\Http\Controllers\FasilitasController::class, 'tarifRemove'])->name('fasilitas.tarif.delete');

        Route::post('booking/save',                 [\App\Http\Controllers\BookingController::class, 'save'])->name('booking.save');
        Route::post('booking/delete',               [\App\Http\Controllers\BookingController::class, 'remove'])->name('booking.delete');
        Route::post('booking/status',               [\App\Http\Controllers\BookingController::class, 'updateStatus'])->name('booking.status');
        Route::post('booking/bayar',                [\App\Http\Controllers\BookingController::class, 'bayar'])->name('booking.bayar');
    });

    // ── Keuangan: Piutang ───────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('piutang',               [\App\Http\Controllers\PiutangController::class, 'index'])->name('piutang.index');
        Route::get('piutang/list',          [\App\Http\Controllers\PiutangController::class, 'list'])->name('piutang.list');
        Route::get('piutang/ringkasan',     [\App\Http\Controllers\PiutangController::class, 'ringkasan'])->name('piutang.ringkasan');
        Route::get('piutang/cicilan/{piutang}', [\App\Http\Controllers\PiutangController::class, 'cicilanList'])->name('piutang.cicilan.list');
    });
    Route::middleware('role:admin,ketua,bendahara,sekretaris')->group(function () {
        Route::post('piutang/save',         [\App\Http\Controllers\PiutangController::class, 'save'])->name('piutang.save');
        Route::post('piutang/delete',       [\App\Http\Controllers\PiutangController::class, 'remove'])->name('piutang.delete');
        Route::post('piutang/cicilan/save', [\App\Http\Controllers\PiutangController::class, 'bayarCicilan'])->name('piutang.cicilan.save');
        Route::post('piutang/macet',        [\App\Http\Controllers\PiutangController::class, 'tandaiMacet'])->name('piutang.macet');
    });

    // ── Keuangan: Kas ───────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('kas',                   [\App\Http\Controllers\KasController::class, 'index'])->name('kas.index');
        Route::get('kas/list',              [\App\Http\Controllers\KasController::class, 'list'])->name('kas.list');
        Route::get('kas/ringkasan',         [\App\Http\Controllers\KasController::class, 'ringkasan'])->name('kas.ringkasan');
        Route::get('kas/cashflow',          [\App\Http\Controllers\KasController::class, 'cashflowBulanan'])->name('kas.cashflow');
        Route::get('kas/kategori',          [\App\Http\Controllers\KasController::class, 'kategoriList'])->name('kas.kategori');
    });
    Route::middleware('role:admin,ketua,bendahara')->group(function () {
        Route::post('kas/save',             [\App\Http\Controllers\KasController::class, 'save'])->name('kas.save');
        Route::post('kas/delete',           [\App\Http\Controllers\KasController::class, 'remove'])->name('kas.delete');
        Route::post('kas/upload-bukti',     [\App\Http\Controllers\KasController::class, 'uploadBukti'])->name('kas.upload-bukti');
        Route::post('kas/kategori/save',    [\App\Http\Controllers\KasController::class, 'kategoriSave'])->name('kas.kategori.save');
    });

    // Data Warga
    Route::middleware('menu.access')->group(function () {
        Route::get('warga',                [\App\Http\Controllers\WargaController::class, 'index'])->name('warga.index');
        Route::get('warga/kk/list',        [\App\Http\Controllers\WargaController::class, 'kkList'])->name('warga.kk.list');
        Route::get('warga/anggota/{kk}',   [\App\Http\Controllers\WargaController::class, 'wargaList'])->name('warga.anggota.list');
        Route::get('warga/kk-lookup',      [\App\Http\Controllers\WargaController::class, 'kkLookup'])->name('warga.kk-lookup');
    });
    Route::middleware('role:admin,sekretaris,ketua')->group(function () {
        Route::post('warga/kk/save',       [\App\Http\Controllers\WargaController::class, 'kkSave'])->name('warga.kk.save');
        Route::post('warga/kk/delete',     [\App\Http\Controllers\WargaController::class, 'kkRemove'])->name('warga.kk.delete');
        Route::post('warga/save',          [\App\Http\Controllers\WargaController::class, 'wargaSave'])->name('warga.save');
        Route::post('warga/delete',        [\App\Http\Controllers\WargaController::class, 'wargaRemove'])->name('warga.delete');
        Route::post('warga/upload-foto',   [\App\Http\Controllers\WargaController::class, 'uploadFoto'])->name('warga.upload-foto');
        Route::post('warga/import',        [\App\Http\Controllers\WargaController::class, 'import'])->name('warga.import');
    });

    // ── Pengumuman RT ────────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        // Halaman pengurus (CRUD)
        Route::get('pengumuman',             [\App\Http\Controllers\PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('pengumuman/list',        [\App\Http\Controllers\PengumumanController::class, 'list'])->name('pengumuman.list');
        // Halaman warga (read-only)
        Route::get('pengumuman-publik',      [\App\Http\Controllers\PengumumanController::class, 'publikasi'])->name('pengumuman.publik');
        Route::get('pengumuman-publik/list', [\App\Http\Controllers\PengumumanController::class, 'publikasiList'])->name('pengumuman.publik.list');
    });
    Route::middleware('role:admin,ketua,sekretaris')->group(function () {
        Route::post('pengumuman/save',       [\App\Http\Controllers\PengumumanController::class, 'save'])->name('pengumuman.save');
        Route::post('pengumuman/hapus-file', [\App\Http\Controllers\PengumumanController::class, 'hapusFile'])->name('pengumuman.hapus-file');
        Route::post('pengumuman/delete',     [\App\Http\Controllers\PengumumanController::class, 'remove'])->name('pengumuman.delete');
    });

    // ── Laporan Warga ────────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('laporan/warga',                    [\App\Http\Controllers\LaporanWargaController::class, 'index'])->name('laporan.warga.index');
        Route::get('laporan/warga/demografi',          [\App\Http\Controllers\LaporanWargaController::class, 'demografi'])->name('laporan.warga.demografi');
        Route::get('laporan/warga/demografi/data',     [\App\Http\Controllers\LaporanWargaController::class, 'demografiData'])->name('laporan.warga.demografi.data');
        Route::get('laporan/warga/daftar',             [\App\Http\Controllers\LaporanWargaController::class, 'daftar'])->name('laporan.warga.daftar');
        Route::get('laporan/warga/daftar/data',        [\App\Http\Controllers\LaporanWargaController::class, 'daftarData'])->name('laporan.warga.daftar.data');
        Route::get('laporan/warga/mutasi',             [\App\Http\Controllers\LaporanWargaController::class, 'mutasi'])->name('laporan.warga.mutasi');
        Route::get('laporan/warga/mutasi/data',        [\App\Http\Controllers\LaporanWargaController::class, 'mutasiData'])->name('laporan.warga.mutasi.data');
    });

    // ── Laporan Keuangan ─────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('laporan',                      [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/realisasi',            [\App\Http\Controllers\LaporanController::class, 'realisasi'])->name('laporan.realisasi');
        Route::get('laporan/realisasi/data',       [\App\Http\Controllers\LaporanController::class, 'realisasiData'])->name('laporan.realisasi.data');
        Route::get('laporan/kas',                  [\App\Http\Controllers\LaporanController::class, 'kas'])->name('laporan.kas');
        Route::get('laporan/kas/data',             [\App\Http\Controllers\LaporanController::class, 'kasData'])->name('laporan.kas.data');
        Route::get('laporan/iuran',                [\App\Http\Controllers\LaporanController::class, 'iuran'])->name('laporan.iuran');
        Route::get('laporan/iuran/data',           [\App\Http\Controllers\LaporanController::class, 'iuranData'])->name('laporan.iuran.data');
        Route::get('laporan/jenis-iuran',          [\App\Http\Controllers\LaporanController::class, 'jenisIuranList'])->name('laporan.jenis');

        Route::get('laporan/anggaran',             [\App\Http\Controllers\AnggaranController::class, 'index'])->name('laporan.anggaran');
        Route::get('laporan/anggaran/list',        [\App\Http\Controllers\AnggaranController::class, 'list'])->name('anggaran.list');
        Route::get('laporan/anggaran/kategori',    [\App\Http\Controllers\AnggaranController::class, 'kategoriLookup'])->name('anggaran.kategori');
    });
    Route::middleware('role:admin,ketua,bendahara')->group(function () {
        Route::post('laporan/anggaran/save',       [\App\Http\Controllers\AnggaranController::class, 'save'])->name('anggaran.save');
        Route::post('laporan/anggaran/delete',     [\App\Http\Controllers\AnggaranController::class, 'remove'])->name('anggaran.delete');
        Route::post('laporan/anggaran/salin',      [\App\Http\Controllers\AnggaranController::class, 'salinTahun'])->name('anggaran.salin');
    });

    // ── Audit Trail ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('audit',         [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
        Route::get('audit/list',    [\App\Http\Controllers\AuditLogController::class, 'list'])->name('audit.list');
        Route::get('audit/users',   [\App\Http\Controllers\AuditLogController::class, 'userList'])->name('audit.users');
        Route::get('audit/moduls',  [\App\Http\Controllers\AuditLogController::class, 'modulList'])->name('audit.moduls');
    });

    // ── Master Role ──────────────────────────────────────────────────────
    Route::middleware('menu.access')->group(function () {
        Route::get('role',                    [\App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
        Route::get('role/list',               [\App\Http\Controllers\RoleController::class, 'list'])->name('role.list');
        Route::get('role/{role}/menu-access', [\App\Http\Controllers\RoleController::class, 'menuAccess'])->name('role.menu-access');
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('role/save',              [\App\Http\Controllers\RoleController::class, 'save'])->name('role.save');
        Route::post('role/delete',            [\App\Http\Controllers\RoleController::class, 'remove'])->name('role.delete');
        Route::post('role/{role}/menu-access',[\App\Http\Controllers\RoleController::class, 'saveMenuAccess'])->name('role.save-menu-access');
    });

    // Halaman & data yang aksesnya dikontrol lewat Master Menu (menu.access)
    Route::middleware('menu.access')->group(function () {
        // Master Menu — hanya admin (roles di menu = admin)
        Route::get('menu',               [\App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');
        Route::get('menu/list',          [\App\Http\Controllers\MenuController::class, 'list'])->name('menu.list');
        Route::get('menu/parent-lookup', [\App\Http\Controllers\MenuController::class, 'parentLookup'])->name('menu.parent');

        // Master User — aksesnya sesuai roles di Master Menu
        Route::get('user',               [\App\Http\Controllers\UserController::class, 'index'])->name('user.index');
        Route::get('user/list',          [\App\Http\Controllers\UserController::class, 'list'])->name('user.list');
        Route::get('user/role-lookup',   [\App\Http\Controllers\UserController::class, 'roleLookup'])->name('user.role-lookup');
        Route::get('user/warga-lookup',  [\App\Http\Controllers\UserController::class, 'wargaLookup'])->name('user.warga-lookup');
    });

    // Operasi tulis — selalu hanya admin, tidak mengikuti setting menu
    Route::middleware('role:admin')->group(function () {
        Route::post('menu/save',   [\App\Http\Controllers\MenuController::class, 'save'])->name('menu.save');
        Route::post('menu/delete', [\App\Http\Controllers\MenuController::class, 'remove'])->name('menu.delete');

        Route::post('user/save',   [\App\Http\Controllers\UserController::class, 'save'])->name('user.save');
        Route::post('user/delete', [\App\Http\Controllers\UserController::class, 'remove'])->name('user.delete');
    });

});
