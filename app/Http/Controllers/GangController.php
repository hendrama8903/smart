<?php

namespace App\Http\Controllers;

use App\Models\Gang;
use Illuminate\Http\Request;

class GangController extends Controller
{
    public function index()
    {
        return view('keuangan.gang');
    }

    public function list()
    {
        return Gang::with(['koordinator.warga'])
            ->withCount('kartuKeluarga as jumlah_kk')
            ->orderBy('nama_gang')
            ->get()
            ->map(fn ($g) => [
                'id'          => $g->id,
                'nama_gang'   => $g->nama_gang,
                'koordinator' => optional(optional($g->koordinator)->warga)->nama,
                'keterangan'  => $g->keterangan,
                'jumlah_kk'   => $g->jumlah_kk,
                'aktif'       => $g->aktif,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'nama_gang'  => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $data['aktif'] = $request->boolean('aktif', true);

        if ($request->filled('id')) {
            Gang::findOrFail($request->id)->update($data);
            $msg = 'Gang berhasil diperbarui.';
        } else {
            Gang::create($data);
            $msg = 'Gang berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $gang = Gang::withCount('kartuKeluarga as jumlah_kk')->findOrFail($request->id);
        if ($gang->jumlah_kk > 0) {
            return response()->json(['ok' => false, 'message' => 'Gang masih memiliki ' . $gang->jumlah_kk . ' KK. Pindahkan KK terlebih dahulu.'], 422);
        }
        $gang->delete();
        return response()->json(['ok' => true, 'message' => 'Gang berhasil dihapus.']);
    }

    public function lookup()
    {
        return Gang::where('aktif', true)->orderBy('nama_gang')->get(['id', 'nama_gang']);
    }

}
