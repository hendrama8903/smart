<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\IuranTagihan;
use App\Models\JenisIuran;
use App\Models\Kas;
use App\Models\KasKategori;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // ─── Halaman index laporan ────────────────────────────────────────────
    public function index()
    {
        return view('laporan.index');
    }

    // ─── 1. Realisasi vs Rencana ──────────────────────────────────────────
    public function realisasi(Request $request)
    {
        return view('laporan.realisasi');
    }

    public function realisasiData(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);

        // Ambil semua pos anggaran tahun ini
        $anggaran = Anggaran::with('kategori')->where('tahun', $tahun)
            ->orderBy('tipe')->orderBy('nama_pos')->get();

        // Realisasi per kategori (grouping dari kas)
        $realisasiMasuk  = Kas::where('tipe', 'masuk')
            ->whereRaw("YEAR(tanggal) = ?", [$tahun])
            ->groupBy('kategori_id')
            ->select('kategori_id', DB::raw('SUM(jumlah) as total'))
            ->pluck('total', 'kategori_id');

        $realisasiKeluar = Kas::where('tipe', 'keluar')
            ->whereRaw("YEAR(tanggal) = ?", [$tahun])
            ->groupBy('kategori_id')
            ->select('kategori_id', DB::raw('SUM(jumlah) as total'))
            ->pluck('total', 'kategori_id');

        // Total realisasi iuran (masuk)
        $realisasiIuran = (float) Kas::where('tipe','masuk')
            ->whereHas('kategori', fn($q)=>$q->where('nama','Iuran'))
            ->whereRaw("YEAR(tanggal)=?",[$tahun])->sum('jumlah');

        $rows = $anggaran->map(function ($a) use ($realisasiMasuk, $realisasiKeluar) {
            $realisasi = $a->tipe === 'masuk'
                ? (float) ($realisasiMasuk[$a->kategori_id] ?? 0)
                : (float) ($realisasiKeluar[$a->kategori_id] ?? 0);
            $rencana   = (float) $a->nominal_rencana;
            $selisih   = $realisasi - $rencana;
            $pct       = $rencana > 0 ? round($realisasi / $rencana * 100, 1) : 0;

            return [
                'id'          => $a->id,
                'tipe'        => $a->tipe,
                'kategori'    => optional($a->kategori)->nama,
                'nama_pos'    => $a->nama_pos,
                'rencana'     => $rencana,
                'realisasi'   => $realisasi,
                'selisih'     => $selisih,
                'pct'         => $pct,
            ];
        });

        // Summary
        $totalRencanaMasuk   = $rows->where('tipe','masuk')->sum('rencana');
        $totalRealisasiMasuk = $rows->where('tipe','masuk')->sum('realisasi');
        $totalRencanaKeluar  = $rows->where('tipe','keluar')->sum('rencana');
        $totalRealisasiKeluar= $rows->where('tipe','keluar')->sum('realisasi');

        // Cashflow per bulan (realisasi)
        $cashflowBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $bln    = str_pad($m, 2, '0', STR_PAD_LEFT);
            $key    = "{$tahun}-{$bln}";
            $masuk  = (float) Kas::where('tipe','masuk')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?",[$key])->sum('jumlah');
            $keluar = (float) Kas::where('tipe','keluar')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?",[$key])->sum('jumlah');
            $cashflowBulan[] = ['bulan'=>$m,'label'=>\Carbon\Carbon::create($tahun,$m)->locale('id')->isoFormat('MMM'),'masuk'=>$masuk,'keluar'=>$keluar];
        }

        return response()->json([
            'rows'                   => $rows->values(),
            'total_rencana_masuk'    => $totalRencanaMasuk,
            'total_realisasi_masuk'  => $totalRealisasiMasuk,
            'total_rencana_keluar'   => $totalRencanaKeluar,
            'total_realisasi_keluar' => $totalRealisasiKeluar,
            'saldo_rencana'          => $totalRencanaMasuk - $totalRencanaKeluar,
            'saldo_realisasi'        => $totalRealisasiMasuk - $totalRealisasiKeluar,
            'cashflow_bulan'         => $cashflowBulan,
        ]);
    }

    // ─── 2. Laporan Kas ───────────────────────────────────────────────────
    public function kas(Request $request)
    {
        return view('laporan.kas');
    }

    public function kasData(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $tipe  = $request->input('tipe');

        $rows = Kas::with(['kategori','pencatat'])
            ->whereRaw("YEAR(tanggal) = ?", [$tahun])
            ->when($tipe, fn($q) => $q->where('tipe',$tipe))
            ->orderBy('tanggal','desc')->orderBy('id','desc')
            ->get()
            ->map(fn($k) => [
                'id'         => $k->id,
                'tanggal'    => $k->tanggal->format('d/m/Y'),
                'bulan'      => $k->tanggal->format('m/Y'),
                'tipe'       => $k->tipe,
                'kategori'   => optional($k->kategori)->nama,
                'jumlah'     => (float) $k->jumlah,
                'keterangan' => $k->keterangan,
                'pencatat'   => optional($k->pencatat)->name,
            ]);

        // Rekap per kategori
        $rekapMasuk  = Kas::with('kategori')->where('tipe','masuk')
            ->whereRaw("YEAR(tanggal)=?",[$tahun])
            ->groupBy('kategori_id')->select('kategori_id',DB::raw('SUM(jumlah) as total'))->get();
        $rekapKeluar = Kas::with('kategori')->where('tipe','keluar')
            ->whereRaw("YEAR(tanggal)=?",[$tahun])
            ->groupBy('kategori_id')->select('kategori_id',DB::raw('SUM(jumlah) as total'))->get();

        return response()->json([
            'rows'          => $rows->values(),
            'total_masuk'   => (float) Kas::where('tipe','masuk')->whereRaw("YEAR(tanggal)=?",[$tahun])->sum('jumlah'),
            'total_keluar'  => (float) Kas::where('tipe','keluar')->whereRaw("YEAR(tanggal)=?",[$tahun])->sum('jumlah'),
            'rekap_masuk'   => $rekapMasuk->map(fn($r)=>['kategori'=>optional($r->kategori)->nama,'total'=>(float)$r->total]),
            'rekap_keluar'  => $rekapKeluar->map(fn($r)=>['kategori'=>optional($r->kategori)->nama,'total'=>(float)$r->total]),
        ]);
    }

    // ─── 3. Laporan Iuran ─────────────────────────────────────────────────
    public function iuran(Request $request)
    {
        return view('laporan.iuran');
    }

    public function iuranData(Request $request)
    {
        $tahun   = (int) $request->input('tahun', now()->year);
        $jenisId = $request->input('jenis_iuran_id');

        // Rekap per bulan per jenis
        $data = IuranTagihan::with('jenisIuran')
            ->whereRaw("YEAR(periode)=?", [$tahun])
            ->when($jenisId, fn($q) => $q->where('jenis_iuran_id',$jenisId))
            ->groupBy(DB::raw("DATE_FORMAT(periode,'%Y-%m')"), 'jenis_iuran_id', 'status')
            ->select(
                DB::raw("DATE_FORMAT(periode,'%Y-%m') as bulan"),
                'jenis_iuran_id', 'status',
                DB::raw('COUNT(*) as jumlah_kk'),
                DB::raw('SUM(nominal) as total_tagihan'),
                DB::raw('SUM(nominal_dibayar) as total_dibayar')
            )->get();

        // Rekap tunggakan per KK
        $tunggakan = IuranTagihan::with(['kartuKeluarga','jenisIuran'])
            ->whereRaw("YEAR(periode)=?", [$tahun])
            ->when($jenisId, fn($q) => $q->where('jenis_iuran_id',$jenisId))
            ->whereIn('status',['belum','sebagian'])
            ->orderBy('periode')
            ->get()
            ->map(fn($t) => [
                'kepala_keluarga' => optional($t->kartuKeluarga)->kepala_keluarga,
                'blok_no'         => optional($t->kartuKeluarga)->alamat_singkat,
                'jenis_iuran'     => optional($t->jenisIuran)->nama,
                'periode'         => optional($t->periode)->format('m/Y'),
                'nominal'         => (float) $t->nominal,
                'nominal_dibayar' => (float) $t->nominal_dibayar,
                'sisa'            => (float) $t->sisa,
                'status'          => $t->status,
            ]);

        return response()->json([
            'data'             => $data->values(),
            'tunggakan'        => $tunggakan->values(),
            'total_tagihan'    => (float) IuranTagihan::whereRaw("YEAR(periode)=?",[$tahun])->when($jenisId,fn($q)=>$q->where('jenis_iuran_id',$jenisId))->sum('nominal'),
            'total_dibayar'    => (float) IuranTagihan::whereRaw("YEAR(periode)=?",[$tahun])->when($jenisId,fn($q)=>$q->where('jenis_iuran_id',$jenisId))->sum('nominal_dibayar'),
            'total_tunggakan'  => (float) IuranTagihan::whereRaw("YEAR(periode)=?",[$tahun])->when($jenisId,fn($q)=>$q->where('jenis_iuran_id',$jenisId))->whereIn('status',['belum','sebagian'])->get()->sum(fn($t)=>$t->sisa),
        ]);
    }

    // Jenis iuran untuk filter
    public function jenisIuranList()
    {
        return JenisIuran::where('aktif',true)->orderBy('nama')->get(['id','nama']);
    }
}
