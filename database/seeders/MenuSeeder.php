<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('menu_permissions')->truncate();
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── 0. Dashboard (standalone, semua role) ────────────────────────
        $this->screen(null, 'Dashboard',   'dashboard', 'DashboardController', 'index', null, 0);

        // ── 1. Data Warga ────────────────────────────────────────────────
        $warga = $this->group('Data Warga', 'users', 10);
            $this->screen($warga, 'KK & Warga',       'users',     'WargaController',          'index', null,                     10);
            $this->screen($warga, 'Master Gang',       'building',  'GangController',           'index', 'admin,ketua,sekretaris', 20);
            $this->screen($warga, 'Koordinator Gang',  'shield',    'KoordinatorGangController','index', 'admin,ketua,sekretaris', 30);

        // ── 2. Keuangan ──────────────────────────────────────────────────
        $keu = $this->group('Keuangan', 'coins', 20);
            $this->screen($keu, 'Iuran Bulanan', 'bill',  'IuranController',     'index', 'admin,ketua,bendahara,koordinator', 10);
            $this->screen($keu, 'Iuran Saya',    'bill',  'IuranSayaController', 'index', 'warga',                            15);
            $this->screen($keu, 'Gang Saya',     'heart', 'MyKoordinatorController', 'index', 'koordinator',                 17);
            $this->screen($keu, 'Kas RT',        'coins', 'KasController',       'index', 'admin,ketua,bendahara',           20);
            $this->screen($keu, 'Piutang',       'card',  'PiutangController',   'index', 'admin,ketua,bendahara',           30);

        // ── 3. Fasilitas ─────────────────────────────────────────────────
        $fas = $this->group('Fasilitas', 'building', 30);
            $this->screen($fas, 'Master Fasilitas',  'building',  'FasilitasController', 'index', 'admin,ketua,bendahara', 10);
            $this->screen($fas, 'Booking Fasilitas', 'clipboard', 'BookingController',   'index', null,                    20);

        // ── 4. Laporan ───────────────────────────────────────────────────
        $lap = $this->group('Laporan', 'file', 40, 'admin,ketua,bendahara,sekretaris');
            $this->screen($lap, 'Laporan Keuangan', 'coins', 'LaporanController',      'index', 'admin,ketua,bendahara',           10);
            $this->screen($lap, 'Laporan Warga',    'users', 'LaporanWargaController', 'index', 'admin,ketua,sekretaris',          20);

        // ── 5. Pengumuman (standalone) ───────────────────────────────────
        $this->screen(null, 'Pengumuman', 'clipboard', 'PengumumanController', 'index', null, 50);

        // ── 6. Pengaturan (admin only) ───────────────────────────────────
        $set = $this->group('Pengaturan', 'settings', 90, 'admin');
            $this->screen($set, 'Master User',  'users',     'UserController',      'index', 'admin',           10);
            $this->screen($set, 'Master Role',  'shield',    'RoleController',      'index', 'admin',           20);
            $this->screen($set, 'Master Menu',  'menu',      'MenuController',      'index', 'admin',           30);
            $this->screen($set, 'Audit Log',    'clipboard', 'AuditLogController',  'index', 'admin,ketua',     40);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function group(string $nama, string $icon, int $urutan, ?string $roles = null): Menu
    {
        return Menu::create([
            'parent_id'  => null,
            'nama'       => $nama,
            'type'       => 'menu',
            'icon'       => $icon,
            'controller' => null,
            'fungsi'     => null,
            'url'        => null,
            'urutan'     => $urutan,
            'roles'      => $roles,
            'aktif'      => true,
        ]);
    }

    private function screen(
        ?Menu $parent,
        string $nama,
        string $icon,
        string $controller,
        string $fungsi,
        ?string $roles,
        int $urutan
    ): Menu {
        return Menu::create([
            'parent_id'  => $parent?->id,
            'nama'       => $nama,
            'type'       => 'screen',
            'icon'       => $icon,
            'controller' => $controller,
            'fungsi'     => $fungsi,
            'url'        => null,
            'urutan'     => $urutan,
            'roles'      => $roles,
            'aktif'      => true,
        ]);
    }
}
