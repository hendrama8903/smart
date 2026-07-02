<?php

namespace App\Http\Controllers;

use App\Models\IuranAlokasi;
use App\Models\IuranPembayaran;
use App\Models\IuranPeriode;
use App\Models\IuranTagihan;
use App\Models\JenisIuran;
use App\Models\Kas;
use App\Models\KartuKeluarga;
use App\Models\KasKategori;
use App\Models\KoordinatorAnggota;
use App\Models\KoordinatorGang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IuranController extends Controller
{
    // ─── Helper: filter KK berdasarkan role ──────────────────────────────
    private function kkFilter(): ?array
    {
        $user = Auth::user();
        if ($user->hasRole('admin', 'ketua', 'bendahara', 'sekretaris')) return null;

        if ($user->warga_id) {
            $koordinator = KoordinatorGang::where('warga_id', $user->warga_id)
                ->where('aktif', true)->first();
            if ($koordinator) {
                return KoordinatorAnggota::where('koordinator_id', $koordinator->id)
                    ->pluck('kartu_keluarga_id')->toArray();
            }
            $kkId = \App\Models\Warga::find($user->warga_id)?->kartu_keluarga_id;
            return $kkId ? [$kkId] : [];
        }
        return [];
    }

    private function myKoordinator(): ?KoordinatorGang
    {
        $user = Auth::user();
        if (! $user->warga_id) return null;
        return KoordinatorGang::where('warga_id', $user->warga_id)
            ->where('aktif', true)->first();
    }

    public function index()
    {
        return view('keuangan.iuran');
    }

    public function collectMobile()
    {
        return view('keuangan.collect-mobile');
    }

    // ─── Context untuk view ───────────────────────────────────────────────
    public function context(): JsonResponse
    {
        $user        = Auth::user();
        $isAdmin     = $user->hasRole('admin', 'ketua', 'bendahara', 'sekretaris');
        $koordinator = $this->myKoordinator();

        return response()->json([
            'is_admin'       => $isAdmin,
            'is_koordinator' => ! $isAdmin && $koordinator !== null,
            'koordinator'    => $koordinator ? [
                'id'   => $koordinator->id,
                'gang' => optional($koordinator->gang)->nama_gang,
            ] : null,
        ]);
    }

    // ─── Master Jenis Iuran ────────────────────────────────────────────────
    public function jenisList(): JsonResponse
    {
        return response()->json(JenisIuran::orderBy('nama')->get());
    }

    public function jenisSave(Request $request): JsonResponse
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

    public function jenisRemove(Request $request): JsonResponse
    {
        $jenis = JenisIuran::withCount('tagihan')->findOrFail($request->id);
        if ($jenis->tagihan_count > 0) {
            return response()->json(['ok' => false, 'message' => 'Jenis iuran sudah memiliki tagihan, tidak bisa dihapus.'], 422);
        }
        $jenis->delete();
        return response()->json(['ok' => true, 'message' => 'Jenis iuran berhasil dihapus.']);
    }

    // ─── PERIODE: list ────────────────────────────────────────────────────
    public function periodeList(Request $request): JsonResponse
    {
        $jenisId = $request->input('jenis_iuran_id');

        $query = IuranPeriode::with('jenisIuran')
            ->when($jenisId, fn ($q) => $q->where('jenis_iuran_id', $jenisId))
            ->orderByDesc('tahun')->orderByDesc('bulan');

        return response()->json($query->get()->map(fn ($p) => [
            'id'               => $p->id,
            'jenis_iuran_id'   => $p->jenis_iuran_id,
            'jenis_iuran'      => optional($p->jenisIuran)->nama,
            'tahun'            => $p->tahun,
            'bulan'            => $p->bulan,
            'label'            => $p->label,
            'status'           => $p->status,
            'tanggal_buka'     => $p->tanggal_buka?->format('d/m/Y'),
            'tanggal_tutup'    => $p->tanggal_tutup?->format('d/m/Y'),
            'snap_total_tagihan'   => (float) $p->snap_total_tagihan,
            'snap_total_terkumpul' => (float) $p->snap_total_terkumpul,
            'snap_total_tunggakan' => (float) $p->snap_total_tunggakan,
        ]));
    }

    // ─── PERIODE: buka (+ auto-generate tagihan) ──────────────────────────
    public function bukaPeriode(Request $request): JsonResponse
    {
        $request->validate([
            'jenis_iuran_id' => ['required', 'exists:jenis_iuran,id'],
            'tahun'          => ['required', 'integer', 'min:2000', 'max:2100'],
            'bulan'          => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $jenis   = JenisIuran::findOrFail($request->jenis_iuran_id);
        $tahun   = (int) $request->tahun;
        $bulan   = $request->filled('bulan') ? (int) $request->bulan : null;

        // Cek duplikat
        $exists = IuranPeriode::where('jenis_iuran_id', $jenis->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->exists();

        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'Periode ini sudah pernah dibuka.'], 422);
        }

        /** @var IuranPeriode $periode */
        $periode = null;
        $dibuat  = 0;

        DB::transaction(function () use ($jenis, $tahun, $bulan, &$periode, &$dibuat) {
            /** @var IuranPeriode $p */
            $p = IuranPeriode::create([
                'jenis_iuran_id' => $jenis->id,
                'tahun'          => $tahun,
                'bulan'          => $bulan,
                'status'         => 'buka',
                'tanggal_buka'   => today(),
                'dibuka_oleh'    => auth()->id(),
            ]);
            $periode = $p;

            $periodeDate = $bulan
                ? \Carbon\Carbon::create($tahun, $bulan, 1)->toDateString()
                : \Carbon\Carbon::create($tahun, 1, 1)->toDateString();

            // Ambil semua KK aktif dan yang sudah ada tagihan (2 query, bukan N+1)
            $allKkIds = KartuKeluarga::where('aktif', true)->pluck('id');
            $existingKkIds = IuranTagihan::where('jenis_iuran_id', $jenis->id)
                ->where('periode', $periodeDate)
                ->pluck('kartu_keluarga_id');

            $newKkIds  = $allKkIds->diff($existingKkIds)->values();
            $now       = now();
            $petugasId = auth()->id();

            foreach ($newKkIds->chunk(200) as $chunk) {
                IuranTagihan::insert(
                    $chunk->map(fn ($kkId) => [
                        'kartu_keluarga_id' => $kkId,
                        'jenis_iuran_id'    => $jenis->id,
                        'periode_id'        => $p->id,
                        'periode'           => $periodeDate,
                        'nominal'           => $jenis->nominal,
                        'nominal_dibayar'   => 0,
                        'status'            => 'belum',
                        'petugas_id'        => $petugasId,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ])->toArray()
                );
            }
            $dibuat = $newKkIds->count();
        });

        return response()->json([
            'ok'      => true,
            'message' => "Periode {$periode->label} dibuka. {$dibuat} tagihan berhasil dibuat.",
            'periode' => ['id' => $periode->id, 'label' => $periode->label],
        ]);
    }

    // ─── PERIODE: tutup buku ──────────────────────────────────────────────
    public function tutupBuku(Request $request): JsonResponse
    {
        $request->validate([
            'periode_id'        => ['required', 'exists:iuran_periode,id'],
            'catatan_penutupan' => ['nullable', 'string', 'max:500'],
        ]);

        $periode = IuranPeriode::findOrFail($request->periode_id);

        if ($periode->status === 'tutup') {
            return response()->json(['ok' => false, 'message' => 'Periode ini sudah ditutup.'], 422);
        }

        DB::transaction(function () use ($periode, $request) {
            // Lock tagihan agar pembayaran yang masuk bersamaan tidak mengubah data sebelum snapshot selesai
            $tagihan = IuranTagihan::where('periode_id', $periode->id)
                ->lockForUpdate()
                ->get();

            $totalTagihan   = $tagihan->sum('nominal');
            $totalTerkumpul = $tagihan->sum('nominal_dibayar');
            $totalTunggakan = $tagihan->sum(fn ($t) => $t->sisa);

            // Flag tagihan belum lunas sebagai tunggakan
            IuranTagihan::where('periode_id', $periode->id)
                ->whereIn('status', ['belum', 'sebagian'])
                ->update(['is_tunggakan' => true]);

            $periode->update([
                'status'               => 'tutup',
                'tanggal_tutup'        => today(),
                'ditutup_oleh'         => auth()->id(),
                'snap_total_tagihan'   => $totalTagihan,
                'snap_total_terkumpul' => $totalTerkumpul,
                'snap_total_tunggakan' => $totalTunggakan,
                'catatan_penutupan'    => $request->catatan_penutupan,
            ]);
        });

        return response()->json([
            'ok'      => true,
            'message' => "Buku periode {$periode->label} berhasil ditutup.",
        ]);
    }

    // ─── Daftar tagihan per periode ───────────────────────────────────────
    public function tagihanList(Request $request): JsonResponse
    {
        $periodeId = $request->input('periode_id');
        $gangId    = $request->input('gang_id');
        $kkIds     = $this->kkFilter();

        // Fallback: filter by periode date jika tidak ada periode_id
        $periodeDate = null;
        if (! $periodeId && $request->filled('periode')) {
            $periodeDate = $request->input('periode') . '-01';
        }

        $query = KartuKeluarga::with(['gang', 'iuranTagihan' => function ($q) use ($periodeId, $periodeDate) {
            if ($periodeId) {
                $q->where('periode_id', $periodeId);
            } elseif ($periodeDate) {
                $q->where('periode', $periodeDate);
            }
        }])
        ->where('aktif', true)
        ->when($kkIds !== null, fn ($q) => $q->whereIn('id', $kkIds))
        ->when($gangId, fn ($q) => $q->where('gang_id', $gangId))
        ->orderByRaw("blok IS NULL, blok")
        ->orderByRaw("CAST(no_rumah AS UNSIGNED)")
        ->get();

        return response()->json($query->map(function ($kk) {
            $tagihan = $kk->iuranTagihan->first();
            return [
                'kk_id'           => $kk->id,
                'blok_no'         => trim(($kk->blok ? $kk->blok . ' ' : '') . ($kk->no_rumah ?? '')),
                'kepala_keluarga' => $kk->kepala_keluarga,
                'gang'            => optional($kk->gang)->nama_gang,
                'gang_id'         => $kk->gang_id,
                'tagihan_id'      => $tagihan?->id,
                'periode_id'      => $tagihan?->periode_id,
                'nominal'         => $tagihan ? (float) $tagihan->nominal : 0,
                'nominal_dibayar' => $tagihan ? (float) $tagihan->nominal_dibayar : 0,
                'sisa'            => $tagihan ? (float) $tagihan->sisa : 0,
                'status'          => $tagihan?->status ?? 'belum',
                'is_tunggakan'    => (bool) ($tagihan?->is_tunggakan),
                'is_keringanan'   => (bool) ($tagihan?->is_keringanan),
                'catatan_khusus'  => $tagihan?->catatan_khusus,
                'is_historis'     => (bool) ($tagihan?->is_historis),
            ];
        }));
    }

    // ─── PEMBAYARAN: simpan dengan alokasi FIFO ───────────────────────────
    public function pembayaranSave(Request $request): JsonResponse
    {
        $request->validate([
            'kartu_keluarga_id' => ['required', 'exists:kartu_keluarga,id'],
            'jenis_iuran_id'    => ['required', 'exists:jenis_iuran,id'],
            'tanggal_bayar'     => ['required', 'date'],
            'jumlah_total'      => ['required', 'numeric', 'min:1'],
            'metode'            => ['nullable', 'string', 'max:50'],
            'keterangan'        => ['nullable', 'string'],
        ]);

        $kkIds = $this->kkFilter();
        if ($kkIds !== null && ! in_array($request->kartu_keluarga_id, $kkIds)) {
            return response()->json(['ok' => false, 'message' => 'Anda tidak memiliki akses ke KK ini.'], 403);
        }

        // Validasi: pastikan ada tagihan yang belum lunas
        $hasTagihan = IuranTagihan::where('kartu_keluarga_id', $request->kartu_keluarga_id)
            ->where('jenis_iuran_id', $request->jenis_iuran_id)
            ->whereIn('status', ['belum', 'sebagian'])
            ->exists();
        if (! $hasTagihan) {
            return response()->json(['ok' => false, 'message' => 'Semua tagihan untuk KK ini sudah lunas.'], 422);
        }

        $jumlah = number_format((float) $request->jumlah_total, 2, '.', '');

        DB::transaction(function () use ($request, $jumlah) {
            // Simpan transaksi pembayaran
            $pembayaran = IuranPembayaran::create([
                'kartu_keluarga_id' => $request->kartu_keluarga_id,
                'jenis_iuran_id'    => $request->jenis_iuran_id,
                'tanggal_bayar'     => $request->tanggal_bayar,
                'jumlah_total'      => $jumlah,
                'metode'            => $request->metode,
                'petugas_id'        => auth()->id(),
                'keterangan'        => $request->keterangan,
            ]);

            if ($request->hasFile('bukti_bayar')) {
                $pembayaran->bukti_bayar = $request->file('bukti_bayar')
                    ->store('iuran/bukti', 'public');
                $pembayaran->save();
            }

            // Alokasi FIFO ke tagihan belum/sebagian lunas (terlama dulu)
            // lockForUpdate mencegah lost-update jika dua pembayaran masuk bersamaan
            $tagihanList = IuranTagihan::where('kartu_keluarga_id', $request->kartu_keluarga_id)
                ->where('jenis_iuran_id', $request->jenis_iuran_id)
                ->whereIn('status', ['belum', 'sebagian'])
                ->orderBy('periode')
                ->lockForUpdate()
                ->get();

            $sisa = $jumlah; // string untuk bcmath
            foreach ($tagihanList as $tagihan) {
                if (bccomp($sisa, '0', 2) <= 0) break;

                $tagihanSisa = number_format($tagihan->sisa, 2, '.', '');
                $alokasi     = bccomp($sisa, $tagihanSisa, 2) >= 0 ? $tagihanSisa : $sisa;

                IuranAlokasi::create([
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id'    => $tagihan->id,
                    'jumlah'        => $alokasi,
                ]);

                $tagihan->nominal_dibayar = bcadd((string) $tagihan->nominal_dibayar, $alokasi, 2);
                $tagihan->tanggal_bayar   = $request->tanggal_bayar;
                $tagihan->petugas_id      = auth()->id();
                $tagihan->updateStatus();

                // Jika lunas, hapus flag tunggakan
                if ($tagihan->status === 'lunas') {
                    $tagihan->is_tunggakan = false;
                    $tagihan->save();
                }

                $sisa = bcsub($sisa, $alokasi, 2);
            }

            // Catat ke kas
            $kategori = KasKategori::where('nama', 'Iuran')->first();
            $kk       = KartuKeluarga::find($request->kartu_keluarga_id);
            $jenis    = JenisIuran::find($request->jenis_iuran_id);

            Kas::create([
                'tanggal'      => $request->tanggal_bayar,
                'kategori_id'  => $kategori?->id,
                'tipe'         => 'masuk',
                'jumlah'       => $jumlah,
                'keterangan'   => 'Iuran ' . optional($jenis)->nama . ' - ' . optional($kk)->kepala_keluarga,
                'ref_tabel'    => 'iuran_pembayaran',
                'ref_id'       => $pembayaran->id,
                'dicatat_oleh' => auth()->id(),
            ]);
        });

        return response()->json(['ok' => true, 'message' => 'Pembayaran berhasil dicatat dan dialokasikan.']);
    }

    // ─── PEMBAYARAN: riwayat per KK ──────────────────────────────────────
    public function pembayaranList(Request $request): JsonResponse
    {
        $request->validate([
            'kartu_keluarga_id' => ['required', 'exists:kartu_keluarga,id'],
            'jenis_iuran_id'    => ['required', 'exists:jenis_iuran,id'],
        ]);

        $kkIds = $this->kkFilter();
        if ($kkIds !== null && ! in_array($request->kartu_keluarga_id, $kkIds)) {
            return response()->json([], 403);
        }

        $list = IuranPembayaran::with(['alokasi.tagihan', 'petugas'])
            ->where('kartu_keluarga_id', $request->kartu_keluarga_id)
            ->where('jenis_iuran_id', $request->jenis_iuran_id)
            ->orderByDesc('tanggal_bayar')
            ->limit(100)
            ->get();

        return response()->json($list->map(fn ($p) => [
            'id'           => $p->id,
            'tanggal_bayar'=> $p->tanggal_bayar?->format('d/m/Y'),
            'jumlah_total' => (float) $p->jumlah_total,
            'metode'       => $p->metode,
            'petugas'      => optional($p->petugas)->name,
            'keterangan'   => $p->keterangan,
            'alokasi'      => $p->alokasi->map(fn ($a) => [
                'periode' => optional($a->tagihan?->periode)->format('m/Y'),
                'jumlah'  => (float) $a->jumlah,
            ]),
        ]));
    }

    // ─── Rekap per periode ────────────────────────────────────────────────
    public function rekapBulanan(Request $request): JsonResponse
    {
        $periodeId = $request->input('periode_id');
        $kkIds     = $this->kkFilter();

        if (! $periodeId) {
            return response()->json(['total_kk' => 0, 'total_tagihan' => 0, 'total_dibayar' => 0,
                'total_sisa' => 0, 'lunas' => 0, 'sebagian' => 0, 'belum' => 0]);
        }

        $tagihan = IuranTagihan::where('periode_id', $periodeId)
            ->when($kkIds !== null, fn ($q) => $q->whereIn('kartu_keluarga_id', $kkIds))
            ->get();

        return response()->json([
            'total_kk'      => $tagihan->count(),
            'total_tagihan' => $tagihan->sum('nominal'),
            'total_dibayar' => $tagihan->sum('nominal_dibayar'),
            'total_sisa'    => $tagihan->sum(fn ($t) => $t->sisa),
            'lunas'         => $tagihan->where('status', 'lunas')->count(),
            'sebagian'      => $tagihan->where('status', 'sebagian')->count(),
            'belum'         => $tagihan->where('status', 'belum')->count(),
        ]);
    }

    // ─── Tandai keringanan ────────────────────────────────────────────────
    public function tandaiKeringanan(Request $request): JsonResponse
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

    // ─── Tunggakan (lintas periode) ───────────────────────────────────────
    public function tunggakan(Request $request): JsonResponse
    {
        $jenisId = $request->input('jenis_iuran_id');
        $gangId  = $request->input('gang_id');
        $kkIds   = $this->kkFilter();

        return response()->json(
            IuranTagihan::with(['kartuKeluarga.gang', 'jenisIuran'])
                ->whereIn('status', ['belum', 'sebagian'])
                ->when($jenisId, fn ($q) => $q->where('jenis_iuran_id', $jenisId))
                ->when($kkIds !== null, fn ($q) => $q->whereIn('kartu_keluarga_id', $kkIds))
                ->when($gangId, fn ($q) => $q->whereHas('kartuKeluarga', fn ($q2) => $q2->where('gang_id', $gangId)))
                ->orderBy('periode')
                ->get()
                ->map(fn ($t) => [
                    'id'              => $t->id,
                    'periode'         => optional($t->periode)->format('m/Y'),
                    'kepala_keluarga' => optional($t->kartuKeluarga)->kepala_keluarga,
                    'blok_no'         => trim((optional($t->kartuKeluarga)->blok ?? '') . ' ' . (optional($t->kartuKeluarga)->no_rumah ?? '')),
                    'gang'            => optional(optional($t->kartuKeluarga)->gang)->nama_gang,
                    'jenis_iuran'     => optional($t->jenisIuran)->nama,
                    'nominal'         => (float) $t->nominal,
                    'nominal_dibayar' => (float) $t->nominal_dibayar,
                    'sisa'            => (float) $t->sisa,
                    'status'          => $t->status,
                    'is_tunggakan'    => (bool) $t->is_tunggakan,
                ])
        );
    }

    // ─── Import tunggakan historis ────────────────────────────────────────
    public function importTunggakan(Request $request): JsonResponse
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
                $data   = array_combine($headers, $row);
                $rowNum = $i + 2;

                if (blank($data['no_kk'] ?? null)) continue;

                $kk = KartuKeluarga::where('no_kk', $data['no_kk'])->first();
                if (! $kk) { $errors[] = "Baris {$rowNum}: No. KK '{$data['no_kk']}' tidak ditemukan."; continue; }

                $jenis = JenisIuran::where('nama', $data['jenis_iuran'])->first();
                if (! $jenis) { $errors[] = "Baris {$rowNum}: Jenis iuran '{$data['jenis_iuran']}' tidak ditemukan."; continue; }

                $periodeStr = trim($data['periode'] ?? '');
                if (! preg_match('/^\d{4}-\d{2}$/', $periodeStr)) {
                    $errors[] = "Baris {$rowNum}: Format periode harus YYYY-MM."; continue;
                }
                $periode        = $periodeStr . '-01';
                $nominalDibayar = (float) ($data['nominal_dibayar'] ?? 0);
                $nominal        = (float) ($data['nominal'] ?? $jenis->nominal);

                $existing = IuranTagihan::where('kartu_keluarga_id', $kk->id)
                    ->where('jenis_iuran_id', $jenis->id)
                    ->where('periode', $periode)->first();

                if ($existing) {
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
}
