<?php

namespace App\Http\Controllers;

use App\Models\KoordinatorAnggota;
use App\Models\KoordinatorGang;
use App\Models\Warga;
use Illuminate\Http\Request;

class KoordinatorGangController extends Controller
{
    public function index()
    {
        return view('keuangan.koordinator');
    }

    // Data untuk grid
    public function list()
    {
        return KoordinatorGang::with(['warga.kartuKeluarga', 'gang'])
            ->orderBy('aktif', 'desc')
            ->orderBy('id')
            ->get()
            ->map(fn ($k) => [
                'id'         => $k->id,
                'warga_id'   => $k->warga_id,
                'gang_id'    => $k->gang_id,
                'nama'       => optional($k->warga)->nama,
                'nik'        => optional($k->warga)->nik,
                'no_telepon' => optional($k->warga)->no_telepon,
                'blok_no'    => optional($k->warga->kartuKeluarga)->alamat_singkat,
                'nama_gang'  => optional($k->gang)->nama_gang,
                'keterangan' => $k->keterangan,
                'aktif'      => $k->aktif,
            ]);
    }

    // Lookup warga tetap + aktif yang belum menjadi koordinator
    public function wargaLookup(Request $request)
    {
        $sudahKoordinator = KoordinatorGang::pluck('warga_id');

        return Warga::with('kartuKeluarga')
            ->where('status_tinggal', 'tetap')
            ->where('status_warga', 'aktif')
            ->whereNotIn('id', $sudahKoordinator)
            ->when($request->filled('q'), fn ($q) =>
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nik', 'like', '%' . $request->q . '%')
            )
            ->when($request->filled('current'), fn ($q) =>
                $q->orWhere('id', $request->current)
            )
            ->orderBy('nama')
            ->limit(30)
            ->get()
            ->map(fn ($w) => [
                'id'     => $w->id,
                'label'  => $w->nama . ' — ' . (optional($w->kartuKeluarga)->blok ?? '') . ' ' . (optional($w->kartuKeluarga)->no_rumah ?? '') . ' (NIK: ' . $w->nik . ')',
                'nama'   => $w->nama,
            ]);
    }

    // Lookup untuk dropdown di form gang (hanya koordinator aktif)
    public function lookup()
    {
        return KoordinatorGang::with('warga')
            ->where('aktif', true)
            ->get()
            ->map(fn ($k) => [
                'id'    => $k->id,
                'label' => optional($k->warga)->nama ?? '—',
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'warga_id'   => ['required', 'exists:warga,id'],
            'gang_id'    => ['nullable', 'exists:gang,id'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
        $data['aktif']   = $request->boolean('aktif', true);
        $data['gang_id'] = $request->gang_id ?: null;

        if ($request->filled('id')) {
            KoordinatorGang::findOrFail($request->id)->update($data);
            $msg = 'Koordinator berhasil diperbarui.';
        } else {
            if (KoordinatorGang::where('warga_id', $request->warga_id)->exists()) {
                return response()->json(['ok' => false, 'message' => 'Warga ini sudah terdaftar sebagai koordinator.'], 422);
            }
            KoordinatorGang::create($data);
            $msg = 'Koordinator berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Anggota koordinator (per KK) ────────────────────────────────────
    public function anggotaList(KoordinatorGang $koordinator)
    {
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

    public function anggotaAdd(Request $request)
    {
        $request->validate([
            'koordinator_id'       => ['required', 'exists:koordinator_gang,id'],
            'kartu_keluarga_id'    => ['required', 'array', 'min:1'],
            'kartu_keluarga_id.*'  => ['exists:kartu_keluarga,id'],
        ]);

        $added = 0; $skipped = 0;

        foreach ($request->kartu_keluarga_id as $kkId) {
            if (KoordinatorAnggota::where('koordinator_id', $request->koordinator_id)
                ->where('kartu_keluarga_id', $kkId)->exists()) {
                $skipped++; continue;
            }
            KoordinatorAnggota::create([
                'koordinator_id'    => $request->koordinator_id,
                'kartu_keluarga_id' => $kkId,
            ]);
            $added++;
        }

        $msg = "{$added} KK berhasil ditambahkan.";
        if ($skipped) $msg .= " {$skipped} sudah terdaftar.";

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function anggotaRemove(Request $request)
    {
        KoordinatorAnggota::findOrFail($request->id)->delete();
        return response()->json(['ok' => true, 'message' => 'KK berhasil dihapus dari koordinator.']);
    }

    // Lookup KK yang belum menjadi anggota koordinator ini
    public function anggotaKkLookup(Request $request, KoordinatorGang $koordinator)
    {
        $sudahAnggota = $koordinator->anggota()->pluck('kartu_keluarga_id');

        return \App\Models\KartuKeluarga::where('aktif', true)
            ->whereNotIn('id', $sudahAnggota)
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

    public function remove(Request $request)
    {
        $k = KoordinatorGang::with('gang')->findOrFail($request->id);

        if ($k->gang) {
            return response()->json([
                'ok'      => false,
                'message' => 'Koordinator ini masih ditugaskan di gang "' . $k->gang->nama_gang . '". Lepas dulu dari gang sebelum menghapus.',
            ], 422);
        }

        $k->delete();
        return response()->json(['ok' => true, 'message' => 'Koordinator berhasil dihapus.']);
    }
}
