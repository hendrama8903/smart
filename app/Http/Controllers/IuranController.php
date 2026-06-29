<?php

namespace App\Http\Controllers;

use App\Models\Gang;
use App\Models\IuranTagihan;
use App\Models\JenisIuran;
use App\Models\Kas;
use App\Models\KartuKeluarga;
use App\Models\KasKategori;
use App\Models\KoordinatorAnggota;
use App\Models\KoordinatorGang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IuranController extends Controller
{
    // ─── Helper: filter KK berdasarkan role user yang login ──────────────
    private function kkFilter(): ?array
    {
        $user = Auth::user();

        // Admin, Ketua, Bendahara, Sekretaris → akses semua KK
        if ($user->hasRole('admin', 'ketua', 'bendahara', 'sekretaris')) {
            return null;
        }

        // Koordinator gang → hanya KK anggotanya
        if ($user->warga_id) {
            $koordinator = KoordinatorGang::where('warga_id', $user->warga_id)
                ->where('aktif', true)->first();
            if ($koordinator) {
                return KoordinatorAnggota::where('koordinator_id', $koordinator->id)
                    ->pluck('kartu_keluarga_id')->toArray();
            }
        }

        // Warga biasa → hanya KK-nya sendiri
        if ($user->warga_id) {
            $kkId = \App\Models\Warga::find($user->warga_id)?->kartu_keluarga_id;
            return $kkId ? [$kkId] : [];
        }

        return []; // tidak punya akses
    }

    // Apakah user ini koordinator gang?
    private function myKoordinator(): ?KoordinatorGang
    {
        $user = Auth::user();
        if (! $user->warga_id) return null;
        return KoordinatorGang::where('warga_id', $user->warga_id)
            ->where('aktif', true)->first();
    }

    // Info context untuk dikirim ke view
    public function context()
    {
        $user = Auth::user();
        $isAdmin    = $user->hasRole('admin', 'ketua', 'bendahara', 'sekretaris');
        $koordinator = $this->myKoordinator();

        return response()->json([
            'is_admin'       => $isAdmin,
            'is_koordinator' => ! $isAdmin && $koordinator !== null,
            'koordinator'    => $koordinator ? [
                'id'        => $koordinator->id,
                'gang'      => optional($koordinator->gang)->nama_gang,
            ] : null,
        ]);
    }

    // ─── Halaman utama iuran ──────────────────────────────────────────────
    public function index()
    {
        return view('keuangan.iuran');
    }

    // ─── Halaman collect mobile ────────────────────────────────────────────
    public function collectMobile()
    {
        return view('keuangan.collect-mobile');
    }

    // ─── Master Jenis Iuran ────────────────────────────────────────────────
    public function jenisList()
    {
        return JenisIuran::orderBy('nama')->get();
    }

    public function jenisSave(Request $request)
    {
        $data = $request->validate([
            'nama'       => ['required', 'string', 'max:100'],
            'nominal'    => ['required', 'numeric', 'min:0'],
            'periode'    => ['required', 'in:bulanan,tahunan'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
        $data['aktif'] = $request->boolean('aktif', true);

        if ($request->filled('id')) {
            JenisIuran::findOrFail($request->id)->update($data);
            $msg = 'Jenis iuran berhasil diperbarui.';
        } else {
            JenisIuran::create($data);
            $msg = 'Jenis iuran berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function jenisRemove(Request $request)
    {
        $jenis = JenisIuran::withCount('tagihan')->findOrFail($request->id);
        if ($jenis->tagihan_count > 0) {
            return response()->json(['ok' => false, 'message' => 'Jenis iuran sudah memiliki tagihan, tidak bisa dihapus.'], 422);
        }
        $jenis->delete();
        return response()->json(['ok' => true, 'message' => 'Jenis iuran berhasil dihapus.']);
    }

    // ─── Daftar tagihan per periode & jenis ───────────────────────────────
    public function tagihanList(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        $jenisId = $request->input('jenis_iuran_id');
        $gangId  = $request->input('gang_id');
        $kkIds   = $this->kkFilter(); // null = semua, array = filter KK

        $query = KartuKeluarga::with(['gang', 'iuranTagihan' => function ($q) use ($periode, $jenisId) {
            $q->where('periode', $periode . '-01');
            if ($jenisId) $q->where('jenis_iuran_id', $jenisId);
        }])
        ->where('aktif', true)
        ->when($kkIds !== null, fn ($q) => $q->whereIn('id', $kkIds))
        ->when($gangId, fn ($q) => $q->where('gang_id', $gangId))
        ->orderByRaw("blok IS NULL, blok")
        ->orderByRaw("CAST(no_rumah AS UNSIGNED)")
        ->get();

        return $query->map(function ($kk) use ($jenisId) {
            $tagihan = $kk->iuranTagihan->when($jenisId, fn ($c) => $c->where('jenis_iuran_id', $jenisId))->first();
            return [
                'kk_id'          => $kk->id,
                'blok_no'        => trim(($kk->blok ? $kk->blok . ' ' : '') . ($kk->no_rumah ?? '')),
                'kepala_keluarga'=> $kk->kepala_keluarga,
                'gang'           => optional($kk->gang)->nama_gang,
                'gang_id'        => $kk->gang_id,
                'tagihan_id'     => $tagihan?->id,
                'jenis_iuran_id' => $tagihan?->jenis_iuran_id,
                'nominal'        => $tagihan ? (float) $tagihan->nominal : 0,
                'nominal_dibayar'=> $tagihan ? (float) $tagihan->nominal_dibayar : 0,
                'sisa'           => $tagihan ? (float) $tagihan->sisa : 0,
                'status'         => $tagihan?->status ?? 'belum',
                'tanggal_bayar'  => $tagihan?->tanggal_bayar?->format('d/m/Y'),
                'keterangan'     => $tagihan?->keterangan,
                'is_keringanan'  => (bool) ($tagihan?->is_keringanan),
                'catatan_khusus' => $tagihan?->catatan_khusus,
                'is_historis'    => (bool) ($tagihan?->is_historis),
            ];
        });
    }

    // ─── Generate tagihan untuk semua KK aktif ────────────────────────────
    public function generateTagihan(Request $request)
    {
        $request->validate([
            'periode'       => ['required', 'date_format:Y-m'],
            'jenis_iuran_id'=> ['required', 'exists:jenis_iuran,id'],
        ]);

        $jenis   = JenisIuran::findOrFail($request->jenis_iuran_id);
        $periode = $request->periode . '-01';
        $kkList  = KartuKeluarga::where('aktif', true)->get();

        $dibuat = 0;
        $sudahAda = 0;

        foreach ($kkList as $kk) {
            $exists = IuranTagihan::where('kartu_keluarga_id', $kk->id)
                ->where('jenis_iuran_id', $jenis->id)
                ->where('periode', $periode)
                ->exists();

            if ($exists) { $sudahAda++; continue; }

            IuranTagihan::create([
                'kartu_keluarga_id' => $kk->id,
                'jenis_iuran_id'    => $jenis->id,
                'periode'           => $periode,
                'nominal'           => $jenis->nominal,
                'nominal_dibayar'   => 0,
                'status'            => 'belum',
                'petugas_id'        => auth()->id(),
            ]);
            $dibuat++;
        }

        return response()->json([
            'ok'      => true,
            'message' => "Tagihan berhasil dibuat: {$dibuat} KK. {$sudahAda} tagihan sudah ada sebelumnya.",
        ]);
    }

    // ─── Input pembayaran ─────────────────────────────────────────────────
    public function bayar(Request $request)
    {
        $request->validate([
            'tagihan_id'     => ['required', 'exists:iuran_tagihan,id'],
            'nominal_bayar'  => ['required', 'numeric', 'min:1'],
            'tanggal_bayar'  => ['required', 'date'],
            'metode'         => ['nullable', 'string', 'max:50'],
            'keterangan'     => ['nullable', 'string'],
        ]);

        $tagihan = IuranTagihan::findOrFail($request->tagihan_id);

        // Validasi: koordinator hanya bisa bayar tagihan anggotanya
        $kkIds = $this->kkFilter();
        if ($kkIds !== null && ! in_array($tagihan->kartu_keluarga_id, $kkIds)) {
            return response()->json(['ok' => false, 'message' => 'Anda tidak memiliki akses ke tagihan ini.'], 403);
        }
        $tambah  = (float) $request->nominal_bayar;

        $tagihan->nominal_dibayar = min(
            (float) $tagihan->nominal,
            (float) $tagihan->nominal_dibayar + $tambah
        );
        $tagihan->tanggal_bayar = $request->tanggal_bayar;
        $tagihan->metode        = $request->metode;
        $tagihan->keterangan    = $request->keterangan;
        $tagihan->petugas_id    = auth()->id();

        // Handle bukti upload
        if ($request->hasFile('bukti_bayar')) {
            if ($tagihan->bukti_bayar) Storage::disk('public')->delete($tagihan->bukti_bayar);
            $tagihan->bukti_bayar = $request->file('bukti_bayar')->store('iuran/bukti', 'public');
        }

        $tagihan->updateStatus();

        // Auto-catat ke kas jika bayar (lunas atau sebagian)
        if ($tagihan->status !== 'belum') {
            $kategori = KasKategori::where('nama', 'Iuran')->first();
            Kas::create([
                'tanggal'    => $request->tanggal_bayar,
                'kategori_id'=> $kategori?->id,
                'tipe'       => 'masuk',
                'jumlah'     => $tambah,
                'keterangan' => 'Iuran ' . optional($tagihan->jenisIuran)->nama . ' - ' . optional($tagihan->kartuKeluarga)->kepala_keluarga,
                'ref_tabel'  => 'iuran_tagihan',
                'ref_id'     => $tagihan->id,
                'dicatat_oleh' => auth()->id(),
            ]);
        }

        return response()->json(['ok' => true, 'message' => 'Pembayaran berhasil dicatat.']);
    }

    // ─── Rekap iuran bulanan ──────────────────────────────────────────────
    public function rekapBulanan(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        $jenisId = $request->input('jenis_iuran_id');
        $kkIds   = $this->kkFilter();

        $tagihan = IuranTagihan::with(['kartuKeluarga.gang', 'jenisIuran'])
            ->where('periode', $periode . '-01')
            ->when($jenisId, fn ($q) => $q->where('jenis_iuran_id', $jenisId))
            ->when($kkIds !== null, fn ($q) => $q->whereIn('kartu_keluarga_id', $kkIds))
            ->get();

        return [
            'total_kk'       => $tagihan->count(),
            'total_tagihan'  => $tagihan->sum('nominal'),
            'total_dibayar'  => $tagihan->sum('nominal_dibayar'),
            'total_sisa'     => $tagihan->sum(fn ($t) => $t->sisa),
            'lunas'          => $tagihan->where('status', 'lunas')->count(),
            'sebagian'       => $tagihan->where('status', 'sebagian')->count(),
            'belum'          => $tagihan->where('status', 'belum')->count(),
        ];
    }

    // ─── Tandai tagihan sebagai keringanan ────────────────────────────────
    public function tandaiKeringanan(Request $request)
    {
        $request->validate([
            'tagihan_id'    => ['required', 'exists:iuran_tagihan,id'],
            'is_keringanan' => ['required', 'boolean'],
            'catatan_khusus'=> ['nullable', 'string', 'max:255'],
        ]);

        IuranTagihan::findOrFail($request->tagihan_id)->update([
            'is_keringanan'  => $request->boolean('is_keringanan'),
            'catatan_khusus' => $request->catatan_khusus,
        ]);

        return response()->json(['ok' => true, 'message' => 'Tagihan berhasil diperbarui.']);
    }

    // ─── Import tunggakan historis dari Excel ─────────────────────────────
    public function importTunggakan(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx', 'max:5120']]);

        try {
            $reader   = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $workbook = $reader->load($request->file('file')->getRealPath());
            $rows     = $workbook->getActiveSheet()->toArray();
            $headers  = array_shift($rows);

            $success = 0;
            $errors  = [];

            foreach ($rows as $i => $row) {
                $data = array_combine($headers, $row);
                $rowNum = $i + 2;

                if (blank($data['no_kk'] ?? null)) continue;

                $kk = KartuKeluarga::where('no_kk', $data['no_kk'])->first();
                if (! $kk) { $errors[] = "Baris {$rowNum}: No. KK '{$data['no_kk']}' tidak ditemukan."; continue; }

                $jenis = JenisIuran::where('nama', $data['jenis_iuran'])->first();
                if (! $jenis) { $errors[] = "Baris {$rowNum}: Jenis iuran '{$data['jenis_iuran']}' tidak ditemukan."; continue; }

                // Periode: format YYYY-MM → YYYY-MM-01
                $periodeStr = trim($data['periode'] ?? '');
                if (! preg_match('/^\d{4}-\d{2}$/', $periodeStr)) {
                    $errors[] = "Baris {$rowNum}: Format periode harus YYYY-MM (contoh: 2024-01)."; continue;
                }
                $periode = $periodeStr . '-01';

                $nominalDibayar = (float) ($data['nominal_dibayar'] ?? 0);
                $nominal        = (float) ($data['nominal'] ?? $jenis->nominal);

                // Cek duplikat
                $existing = IuranTagihan::where('kartu_keluarga_id', $kk->id)
                    ->where('jenis_iuran_id', $jenis->id)
                    ->where('periode', $periode)
                    ->first();

                if ($existing) {
                    // Update jika sudah ada
                    $existing->nominal_dibayar = min($nominal, $nominalDibayar);
                    $existing->is_historis     = true;
                    $existing->catatan_khusus  = $data['catatan'] ?? null;
                    $existing->is_keringanan   = filter_var($data['keringanan'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    if ($nominalDibayar > 0 && ! $existing->tanggal_bayar) {
                        $existing->tanggal_bayar = $data['tanggal_bayar'] ?: now()->toDateString();
                    }
                    $existing->updateStatus();
                } else {
                    $tagihan = IuranTagihan::create([
                        'kartu_keluarga_id' => $kk->id,
                        'jenis_iuran_id'    => $jenis->id,
                        'periode'           => $periode,
                        'nominal'           => $nominal,
                        'nominal_dibayar'   => min($nominal, $nominalDibayar),
                        'tanggal_bayar'     => $nominalDibayar > 0 ? ($data['tanggal_bayar'] ?: null) : null,
                        'keterangan'        => $data['keterangan'] ?? null,
                        'catatan_khusus'    => $data['catatan'] ?? null,
                        'is_keringanan'     => filter_var($data['keringanan'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'is_historis'       => true,
                        'petugas_id'        => auth()->id(),
                        'status'            => 'belum',
                    ]);
                    $tagihan->updateStatus();
                }

                $success++;
            }

            return response()->json([
                'ok'      => true,
                'message' => "{$success} data tunggakan berhasil diimport." . (count($errors) ? ' ' . count($errors) . ' baris dilewati.' : ''),
                'errors'  => $errors,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'File tidak valid: ' . $e->getMessage()], 422);
        }
    }

    // ─── Laporan tunggakan ────────────────────────────────────────────────
    public function tunggakan(Request $request)
    {
        $jenisId = $request->input('jenis_iuran_id');
        $gangId  = $request->input('gang_id');
        $kkIds   = $this->kkFilter();

        return IuranTagihan::with(['kartuKeluarga.gang', 'jenisIuran'])
            ->whereIn('status', ['belum', 'sebagian'])
            ->when($jenisId, fn ($q) => $q->where('jenis_iuran_id', $jenisId))
            ->when($kkIds !== null, fn ($q) => $q->whereIn('kartu_keluarga_id', $kkIds))
            ->when($gangId, fn ($q) => $q->whereHas('kartuKeluarga', fn ($q2) => $q2->where('gang_id', $gangId)))
            ->orderBy('periode')
            ->get()
            ->map(fn ($t) => [
                'id'             => $t->id,
                'periode'        => optional($t->periode)->format('m/Y'),
                'kepala_keluarga'=> optional($t->kartuKeluarga)->kepala_keluarga,
                'blok_no'        => trim((optional($t->kartuKeluarga)->blok ?? '') . ' ' . (optional($t->kartuKeluarga)->no_rumah ?? '')),
                'gang'           => optional(optional($t->kartuKeluarga)->gang)->nama_gang,
                'jenis_iuran'    => optional($t->jenisIuran)->nama,
                'nominal'        => (float) $t->nominal,
                'nominal_dibayar'=> (float) $t->nominal_dibayar,
                'sisa'           => (float) $t->sisa,
                'status'         => $t->status,
            ]);
    }
}
