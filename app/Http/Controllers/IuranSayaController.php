<?php

namespace App\Http\Controllers;

use App\Models\IuranTagihan;
use App\Models\JenisIuran;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IuranSayaController extends Controller
{
    // KK milik user yang login
    private function myKkId(): ?int
    {
        $wargaId = Auth::user()->warga_id;
        if (! $wargaId) return null;
        return Warga::find($wargaId)?->kartu_keluarga_id;
    }

    public function index()
    {
        $kkId = $this->myKkId();
        return view('keuangan.iuran-saya', compact('kkId'));
    }

    // Daftar tagihan milik KK saya
    public function list(Request $request)
    {
        $kkId = $this->myKkId();
        if (! $kkId) return response()->json([]);

        $jenisId = $request->input('jenis_iuran_id');
        $tahun   = $request->input('tahun', now()->year);

        return IuranTagihan::with('jenisIuran')
            ->where('kartu_keluarga_id', $kkId)
            ->when($jenisId, fn ($q) => $q->where('jenis_iuran_id', $jenisId))
            ->whereRaw("YEAR(periode) = ?", [$tahun])
            ->orderBy('periode', 'desc')
            ->get()
            ->map(fn ($t) => [
                'id'               => $t->id,
                'periode'          => optional($t->periode)->format('m/Y'),
                'bulan'            => optional($t->periode)->locale('id')->isoFormat('MMMM YYYY'),
                'jenis_iuran'      => optional($t->jenisIuran)->nama,
                'nominal'          => (float) $t->nominal,
                'nominal_dibayar'  => (float) $t->nominal_dibayar,
                'sisa'             => (float) $t->sisa,
                'status'           => $t->status,
                'tanggal_bayar'    => $t->tanggal_bayar?->format('d/m/Y'),
                'is_keringanan'    => (bool) $t->is_keringanan,
                'is_tunggakan'     => (bool) $t->is_tunggakan,
                'catatan_khusus'   => $t->catatan_khusus,
            ]);
    }

    // Ringkasan tagihan saya
    public function ringkasan(Request $request)
    {
        $kkId = $this->myKkId();
        if (! $kkId) return response()->json(['lunas' => 0, 'sebagian' => 0, 'belum' => 0, 'total_tunggakan' => 0]);

        $tahun   = $request->input('tahun', now()->year);
        $tagihan = IuranTagihan::where('kartu_keluarga_id', $kkId)
            ->whereRaw("YEAR(periode) = ?", [$tahun])
            ->get();

        return [
            'lunas'          => $tagihan->where('status', 'lunas')->count(),
            'sebagian'       => $tagihan->where('status', 'sebagian')->count(),
            'belum'          => $tagihan->where('status', 'belum')->count(),
            'total_tagihan'  => (float) $tagihan->sum('nominal'),
            'total_dibayar'  => (float) $tagihan->sum('nominal_dibayar'),
            'total_tunggakan'=> (float) $tagihan->whereIn('status', ['belum','sebagian'])->sum(fn ($t) => $t->sisa),
        ];
    }

    // Jenis iuran untuk filter
    public function jenisList()
    {
        return JenisIuran::where('aktif', true)->orderBy('nama')->get(['id', 'nama']);
    }
}
