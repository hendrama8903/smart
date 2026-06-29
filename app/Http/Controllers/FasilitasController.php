<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\TarifFasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        return view('keuangan.fasilitas');
    }

    // ─── Master Fasilitas ──────────────────────────────────────────────
    public function list()
    {
        return Fasilitas::withCount('tarif as jumlah_tarif')->orderBy('nama')->get()
            ->map(fn ($f) => [
                'id'           => $f->id,
                'nama'         => $f->nama,
                'deskripsi'    => $f->deskripsi,
                'satuan'       => $f->satuan,
                'jumlah_tarif' => $f->jumlah_tarif,
                'aktif'        => $f->aktif,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'satuan'    => ['required', 'in:sesi,hari,unit,jam'],
        ]);
        $data['aktif'] = $request->boolean('aktif', true);

        if ($request->filled('id')) {
            Fasilitas::findOrFail($request->id)->update($data);
            $msg = 'Fasilitas berhasil diperbarui.';
        } else {
            Fasilitas::create($data);
            $msg = 'Fasilitas berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $f = Fasilitas::withCount('tarif')->findOrFail($request->id);
        if ($f->tarif_count > 0) {
            return response()->json(['ok' => false, 'message' => 'Hapus tarif terlebih dahulu.'], 422);
        }
        $f->delete();
        return response()->json(['ok' => true, 'message' => 'Fasilitas berhasil dihapus.']);
    }

    // ─── Tarif ────────────────────────────────────────────────────────
    public function tarifList(Fasilitas $fasilitas)
    {
        return $fasilitas->tarif()->orderBy('kategori')->orderBy('nama_tarif')->get();
    }

    public function tarifSave(Request $request)
    {
        $data = $request->validate([
            'fasilitas_id'    => ['required', 'exists:fasilitas,id'],
            'nama_tarif'      => ['required', 'string', 'max:100'],
            'kategori'        => ['required', 'in:warga,luar_warga'],
            'nominal_total'   => ['required', 'numeric', 'min:0'],
            'nominal_kas_rt'  => ['required', 'numeric', 'min:0'],
            'nominal_lain'    => ['nullable', 'numeric', 'min:0'],
            'keterangan_lain' => ['nullable', 'string', 'max:100'],
        ]);
        $data['nominal_lain'] = $request->nominal_lain ?? 0;
        $data['aktif']        = $request->boolean('aktif', true);

        if ($request->filled('id')) {
            TarifFasilitas::findOrFail($request->id)->update($data);
            $msg = 'Tarif berhasil diperbarui.';
        } else {
            TarifFasilitas::create($data);
            $msg = 'Tarif berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function tarifRemove(Request $request)
    {
        TarifFasilitas::findOrFail($request->id)->delete();
        return response()->json(['ok' => true, 'message' => 'Tarif berhasil dihapus.']);
    }

    // ─── Lookup untuk booking form ─────────────────────────────────────
    public function lookup()
    {
        return Fasilitas::where('aktif', true)->orderBy('nama')->get(['id', 'nama', 'satuan']);
    }

    public function tarifLookup(Request $request)
    {
        return TarifFasilitas::where('fasilitas_id', $request->fasilitas_id)
            ->where('kategori', $request->kategori ?? 'warga')
            ->where('aktif', true)
            ->orderBy('nama_tarif')
            ->get();
    }
}
