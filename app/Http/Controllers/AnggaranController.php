<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\KasKategori;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index()
    {
        return view('laporan.anggaran');
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        return Anggaran::with('kategori')
            ->where('tahun', $tahun)
            ->orderBy('tipe')->orderBy('nama_pos')
            ->get()
            ->map(fn ($a) => [
                'id'              => $a->id,
                'tahun'           => $a->tahun,
                'tipe'            => $a->tipe,
                'kategori_id'     => $a->kategori_id,
                'kategori'        => optional($a->kategori)->nama,
                'nama_pos'        => $a->nama_pos,
                'nominal_rencana' => (float) $a->nominal_rencana,
                'keterangan'      => $a->keterangan,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'tahun'           => ['required', 'integer', 'min:2020', 'max:2099'],
            'tipe'            => ['required', 'in:masuk,keluar'],
            'kategori_id'     => ['nullable', 'exists:kas_kategori,id'],
            'nama_pos'        => ['required', 'string', 'max:100'],
            'nominal_rencana' => ['required', 'numeric', 'min:0'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
        ]);
        $data['kategori_id'] = $request->kategori_id ?: null;

        if ($request->filled('id')) {
            Anggaran::findOrFail($request->id)->update($data);
            $msg = 'Anggaran berhasil diperbarui.';
        } else {
            Anggaran::create($data);
            $msg = 'Anggaran berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        Anggaran::findOrFail($request->id)->delete();
        return response()->json(['ok' => true, 'message' => 'Anggaran berhasil dihapus.']);
    }

    public function kategoriLookup(Request $request)
    {
        return KasKategori::when($request->filled('tipe'), fn ($q) => $q->where('tipe', $request->tipe))
            ->orderBy('nama')->get(['id', 'nama', 'tipe']);
    }

    public function salinTahun(Request $request)
    {
        $request->validate([
            'dari_tahun' => ['required', 'integer'],
            'ke_tahun'   => ['required', 'integer'],
        ]);

        $source = Anggaran::where('tahun', $request->dari_tahun)->get();
        $count  = 0;

        foreach ($source as $a) {
            if (! Anggaran::where('tahun', $request->ke_tahun)->where('nama_pos', $a->nama_pos)->where('tipe', $a->tipe)->exists()) {
                Anggaran::create([
                    'tahun'           => $request->ke_tahun,
                    'tipe'            => $a->tipe,
                    'kategori_id'     => $a->kategori_id,
                    'nama_pos'        => $a->nama_pos,
                    'nominal_rencana' => $a->nominal_rencana,
                    'keterangan'      => $a->keterangan,
                ]);
                $count++;
            }
        }

        return response()->json(['ok' => true, 'message' => "{$count} pos anggaran berhasil disalin ke tahun {$request->ke_tahun}."]);
    }
}
