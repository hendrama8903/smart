<?php

namespace Tests\Feature;

use App\Models\Kas;
use App\Models\KasKategori;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasRtTest extends TestCase
{
    use RefreshDatabase;

    private KasKategori $kategoriMasuk;
    private KasKategori $kategoriKeluar;

    protected function setUp(): void
    {
        parent::setUp();

        // roles table sudah di-seed oleh migration create_roles_table.
        // kas_kategori juga sudah punya kategori bawaan (Iuran, Operasional, dst.) dari migration,
        // jadi kategori uji di sini sengaja diberi nama unik supaya tidak rancu dengan data bawaan.
        $this->kategoriMasuk  = KasKategori::create(['nama' => 'Uji Kategori Masuk', 'tipe' => 'masuk']);
        $this->kategoriKeluar = KasKategori::create(['nama' => 'Uji Kategori Keluar', 'tipe' => 'keluar']);
    }

    private function userWithRole(string $roleNama): User
    {
        $role = Role::where('nama', $roleNama)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id]);
    }

    // ───────────────────────── Akses halaman ─────────────────────────

    public function test_tamu_tidak_bisa_mengakses_halaman_kas(): void
    {
        $this->get(route('kas.index'))->assertRedirect(route('login'));
    }

    public function test_user_yang_login_bisa_melihat_halaman_kas(): void
    {
        $user = $this->userWithRole('bendahara');

        $this->actingAs($user)->get(route('kas.index'))->assertOk();
    }

    // ───────────────────────── Kas masuk ─────────────────────────

    public function test_bendahara_dapat_mencatat_transaksi_kas_masuk(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'masuk',
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 150000,
            'keterangan'  => 'Iuran bulan Juli',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('kas', [
            'tipe'         => 'masuk',
            'kategori_id'  => $this->kategoriMasuk->id,
            'jumlah'       => 150000,
            'keterangan'   => 'Iuran bulan Juli',
            'dicatat_oleh' => $bendahara->id,
        ]);
    }

    // ───────────────────────── Kas keluar ─────────────────────────

    public function test_bendahara_dapat_mencatat_transaksi_kas_keluar(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'keluar',
            'kategori_id' => $this->kategoriKeluar->id,
            'jumlah'      => 75000,
            'keterangan'  => 'Beli alat kebersihan',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('kas', [
            'tipe'         => 'keluar',
            'kategori_id'  => $this->kategoriKeluar->id,
            'jumlah'       => 75000,
            'keterangan'   => 'Beli alat kebersihan',
            'dicatat_oleh' => $bendahara->id,
        ]);
    }

    // ───────────────────────── Validasi ─────────────────────────

    public function test_kas_save_menolak_jika_field_wajib_kosong(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'tipe' => 'masuk',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tanggal', 'kategori_id', 'jumlah']);
    }

    public function test_kas_save_menolak_tipe_yang_tidak_valid(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'transfer', // bukan masuk/keluar
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 10000,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['tipe']);
    }

    public function test_kas_save_menolak_jumlah_nol_atau_negatif(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'masuk',
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 0,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['jumlah']);
    }

    // ───────────────────────── Otorisasi role ─────────────────────────

    public function test_role_yang_tidak_berwenang_tidak_bisa_menyimpan_kas(): void
    {
        // Sesuai routes/web.php, hanya admin, ketua, bendahara yang boleh POST kas/save
        $sekretaris = $this->userWithRole('sekretaris');

        $response = $this->actingAs($sekretaris)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'masuk',
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 50000,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('kas', ['jumlah' => 50000]);
    }

    public function test_warga_tidak_bisa_menyimpan_kas(): void
    {
        $warga = $this->userWithRole('warga');

        $response = $this->actingAs($warga)->postJson(route('kas.save'), [
            'tanggal'     => '2026-07-02',
            'tipe'        => 'masuk',
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 50000,
        ]);

        $response->assertForbidden();
    }

    // ───────────────────────── Ubah & hapus ─────────────────────────

    public function test_bendahara_dapat_mengubah_transaksi_kas(): void
    {
        $bendahara = $this->userWithRole('bendahara');
        $kas = Kas::create([
            'tanggal'      => '2026-07-01',
            'tipe'         => 'masuk',
            'kategori_id'  => $this->kategoriMasuk->id,
            'jumlah'       => 100000,
            'keterangan'   => 'Awal',
            'dicatat_oleh' => $bendahara->id,
        ]);

        $response = $this->actingAs($bendahara)->postJson(route('kas.save'), [
            'id'          => $kas->id,
            'tanggal'     => '2026-07-01',
            'tipe'        => 'masuk',
            'kategori_id' => $this->kategoriMasuk->id,
            'jumlah'      => 120000,
            'keterangan'  => 'Direvisi',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('kas', [
            'id'         => $kas->id,
            'jumlah'     => 120000,
            'keterangan' => 'Direvisi',
        ]);
    }

    public function test_bendahara_dapat_menghapus_transaksi_kas(): void
    {
        $bendahara = $this->userWithRole('bendahara');
        $kas = Kas::create([
            'tanggal'      => '2026-07-01',
            'tipe'         => 'keluar',
            'kategori_id'  => $this->kategoriKeluar->id,
            'jumlah'       => 30000,
            'keterangan'   => 'Beli sapu',
            'dicatat_oleh' => $bendahara->id,
        ]);

        $response = $this->actingAs($bendahara)->postJson(route('kas.delete'), ['id' => $kas->id]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseMissing('kas', ['id' => $kas->id]);
    }

    // ───────────────────────── List & ringkasan ─────────────────────────

    public function test_kas_list_dapat_difilter_berdasarkan_tipe_dan_bulan(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        Kas::create(['tanggal' => '2026-07-05', 'tipe' => 'masuk', 'kategori_id' => $this->kategoriMasuk->id, 'jumlah' => 100000, 'keterangan' => 'Iuran Juli', 'dicatat_oleh' => $bendahara->id]);
        Kas::create(['tanggal' => '2026-07-06', 'tipe' => 'keluar', 'kategori_id' => $this->kategoriKeluar->id, 'jumlah' => 40000, 'keterangan' => 'Beli alat', 'dicatat_oleh' => $bendahara->id]);
        Kas::create(['tanggal' => '2026-06-20', 'tipe' => 'masuk', 'kategori_id' => $this->kategoriMasuk->id, 'jumlah' => 200000, 'keterangan' => 'Iuran Juni', 'dicatat_oleh' => $bendahara->id]);

        $response = $this->actingAs($bendahara)->getJson(route('kas.list', ['bulan' => '2026-07', 'tipe' => 'masuk']));

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertSame(100000, (int) $data[0]['jumlah']);
    }

    public function test_kas_ringkasan_menghitung_saldo_dan_net_bulan_dengan_benar(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        // Bulan berjalan: masuk 200rb, keluar 50rb -> net 150rb
        Kas::create(['tanggal' => '2026-07-02', 'tipe' => 'masuk', 'kategori_id' => $this->kategoriMasuk->id, 'jumlah' => 200000, 'keterangan' => 'Iuran', 'dicatat_oleh' => $bendahara->id]);
        Kas::create(['tanggal' => '2026-07-03', 'tipe' => 'keluar', 'kategori_id' => $this->kategoriKeluar->id, 'jumlah' => 50000, 'keterangan' => 'Beli alat', 'dicatat_oleh' => $bendahara->id]);
        // Bulan lalu, ikut menambah saldo total tapi tidak masuk hitungan bulan berjalan
        Kas::create(['tanggal' => '2026-06-10', 'tipe' => 'masuk', 'kategori_id' => $this->kategoriMasuk->id, 'jumlah' => 500000, 'keterangan' => 'Iuran lalu', 'dicatat_oleh' => $bendahara->id]);

        $response = $this->actingAs($bendahara)->getJson(route('kas.ringkasan', ['bulan' => '2026-07']));

        $response->assertOk()->assertJson([
            'saldo_total'  => 650000.0, // (200rb+500rb) - 50rb
            'masuk_bulan'  => 200000.0,
            'keluar_bulan' => 50000.0,
            'net_bulan'    => 150000.0,
        ]);
    }

    // ───────────────────────── Kategori ─────────────────────────

    public function test_kategori_list_terfilter_sesuai_tipe(): void
    {
        $bendahara = $this->userWithRole('bendahara');

        $response = $this->actingAs($bendahara)->getJson(route('kas.kategori', ['tipe' => 'masuk']));

        $response->assertOk();
        $data = $response->json();

        // Semua hasil harus tipe masuk (tidak boleh kebocoran kategori keluar)
        foreach ($data as $row) {
            $this->assertSame('masuk', $row['tipe']);
        }
        $this->assertContains('Uji Kategori Masuk', array_column($data, 'nama'));
        $this->assertNotContains('Uji Kategori Keluar', array_column($data, 'nama'));
    }

    public function test_admin_dapat_menambah_kategori_kas_baru(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->postJson(route('kas.kategori.save'), [
            'nama' => 'Sewa Aula',
            'tipe' => 'masuk',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('kas_kategori', ['nama' => 'Sewa Aula', 'tipe' => 'masuk']);
    }
}
