<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\KoordinatorAnggota;
use App\Models\KoordinatorGang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyKoordinatorController extends Controller
{
    private function myKoordinator(): ?KoordinatorGang
    {
        $wargaId = Auth::user()->warga_id;
        if (! $wargaId) return null;

        return KoordinatorGang::where('warga_id', $wargaId)
            ->where('aktif', true)
            ->with(['warga', 'gang'])
            ->first();
    }

    public function index()
    {
        $koordinator = $this->myKoordinator();
        return view('keuangan.my-koordinator', compact('koordinator'));
    }

    // ─── Data KK anggota saya ─────────────────────────────────────────────
    public function anggotaList()
    {
        $koordinator = $this->myKoordinator();
        if (! $koordinator) return response()->json([]);

        return $koordinator->anggota()
            ->with(['kartuKeluarga'])
            ->get()
            ->map(fn ($a) => [
                'id'              => $a->id,
                'kk_id'           => $a->kartu_keluarga_id,
                'no_kk'           => optional($a->kartuKeluarga)->no_kk,
                'kepala_keluarga' => optional($a->kartuKeluarga)->kepala_keluarga,
                'blok_no'         => optional($a->kartuKeluarga)->alamat_singkat,
                'no_telepon'      => optional($a->kartuKeluarga)->no_telepon,
                'jumlah_jiwa'     => optional($a->kartuKeluarga)->warga()->count(),
            ]);
    }

    // ─── Tambah KK sebagai anggota ───────────────────────────────────────
    public function anggotaAdd(Request $request)
    {
        $koordinator = $this->myKoordinator();
        if (! $koordinator) abort(403, 'Anda tidak terdaftar sebagai koordinator.');

        $request->validate([
            'kartu_keluarga_id'    => ['required', 'array', 'min:1'],
            'kartu_keluarga_id.*'  => ['exists:kartu_keluarga,id'],
        ]);

        $added = 0; $skipped = 0;
        foreach ($request->kartu_keluarga_id as $kkId) {
            if (KoordinatorAnggota::where('koordinator_id', $koordinator->id)
                ->where('kartu_keluarga_id', $kkId)->exists()) {
                $skipped++; continue;
            }
            KoordinatorAnggota::create([
                'koordinator_id'    => $koordinator->id,
                'kartu_keluarga_id' => $kkId,
            ]);
            $added++;
        }

        $msg = "{$added} KK berhasil ditambahkan.";
        if ($skipped) $msg .= " {$skipped} sudah terdaftar.";

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Hapus KK dari anggota ────────────────────────────────────────────
    public function anggotaRemove(Request $request)
    {
        $koordinator = $this->myKoordinator();
        if (! $koordinator) abort(403);

        KoordinatorAnggota::where('id', $request->id)
            ->where('koordinator_id', $koordinator->id)
            ->firstOrFail()
            ->delete();

        return response()->json(['ok' => true, 'message' => 'KK berhasil dihapus dari daftar.']);
    }

    // ─── Lookup KK yang belum terdaftar sebagai anggota saya ─────────────
    public function kkLookup(Request $request)
    {
        $koordinator = $this->myKoordinator();
        if (! $koordinator) return response()->json([]);

        $sudah = $koordinator->anggota()->pluck('kartu_keluarga_id');

        return KartuKeluarga::where('aktif', true)
            ->whereNotIn('id', $sudah)
            ->when($request->filled('q'), fn ($q) =>
                $q->where('kepala_keluarga', 'like', '%' . $request->q . '%')
                  ->orWhere('no_kk', 'like', '%' . $request->q . '%')
                  ->orWhere('blok', 'like', '%' . $request->q . '%')
            )
            ->orderByRaw("blok IS NULL, blok")
            ->orderByRaw("CAST(no_rumah AS UNSIGNED)")
            ->limit(50)
            ->get()
            ->map(fn ($kk) => [
                'id'    => $kk->id,
                'label' => ($kk->alamat_singkat ? $kk->alamat_singkat . ' — ' : '') . $kk->kepala_keluarga,
            ]);
    }
}
