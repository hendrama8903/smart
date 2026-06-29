<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\PiutangCicilan;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PiutangController extends Controller
{
    public function index()
    {
        return view('keuangan.piutang');
    }

    public function list()
    {
        return Piutang::with(['kartuKeluarga', 'pencatat'])
            ->withSum('cicilan as total_cicilan', 'jumlah')
            ->orderBy('status')
            ->orderBy('tanggal_pinjam', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id'               => $p->id,
                'kartu_keluarga_id'=> $p->kartu_keluarga_id,
                'nama_peminjam'    => $p->nama_peminjam,
                'kepala_kk'        => optional($p->kartuKeluarga)->kepala_keluarga,
                'blok_no'          => optional($p->kartuKeluarga)?->alamat_singkat,
                'jumlah'           => (float) $p->jumlah,
                'jumlah_kembali'   => (float) $p->jumlah_kembali,
                'sisa'             => (float) $p->sisa,
                'tanggal_pinjam'   => $p->tanggal_pinjam?->format('d/m/Y'),
                'jatuh_tempo'      => $p->jatuh_tempo?->format('d/m/Y'),
                'tanggal_lunas'    => $p->tanggal_lunas?->format('d/m/Y'),
                'status'           => $p->status,
                'keterangan'       => $p->keterangan,
                'pencatat'         => optional($p->pencatat)->name,
            ]);
    }

    public function cicilanList(Piutang $piutang)
    {
        return $piutang->cicilan()->with('pencatat')->orderBy('tanggal', 'desc')->get()
            ->map(fn ($c) => [
                'id'         => $c->id,
                'piutang_id' => $c->piutang_id,
                'tanggal'    => $c->tanggal->format('d/m/Y'),
                'tanggal_raw'=> $c->tanggal->format('Y-m-d'),
                'jumlah'     => (float) $c->jumlah,
                'keterangan' => $c->keterangan,
                'pencatat'   => optional($c->pencatat)->name,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'kartu_keluarga_id' => ['nullable', 'exists:kartu_keluarga,id'],
            'nama_peminjam'     => ['required', 'string', 'max:100'],
            'jumlah'            => ['required', 'numeric', 'min:1'],
            'tanggal_pinjam'    => ['required', 'date'],
            'jatuh_tempo'       => ['nullable', 'date'],
            'keterangan'        => ['nullable', 'string', 'max:255'],
        ]);

        $data['status']       = 'aktif';
        $data['dicatat_oleh'] = auth()->id();
        $data['kartu_keluarga_id'] = $request->kartu_keluarga_id ?: null;

        if ($request->filled('id')) {
            Piutang::findOrFail($request->id)->update($data);
            $msg = 'Piutang berhasil diperbarui.';
        } else {
            Piutang::create($data);
            $msg = 'Piutang berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $p = Piutang::findOrFail($request->id);
        if ($p->bukti) Storage::disk('public')->delete($p->bukti);
        $p->delete();
        return response()->json(['ok' => true, 'message' => 'Data piutang berhasil dihapus.']);
    }

    // Input pengembalian cicilan
    public function bayarCicilan(Request $request)
    {
        $request->validate([
            'piutang_id' => ['required', 'exists:piutang,id'],
            'jumlah'     => ['required', 'numeric', 'min:1'],
            'tanggal'    => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $piutang = Piutang::findOrFail($request->piutang_id);

        PiutangCicilan::create([
            'piutang_id'  => $piutang->id,
            'tanggal'     => $request->tanggal,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'dicatat_oleh'=> auth()->id(),
        ]);

        // Update total kembali
        $piutang->jumlah_kembali = $piutang->cicilan()->sum('jumlah');

        if ($piutang->jumlah_kembali >= $piutang->jumlah) {
            $piutang->status        = 'lunas';
            $piutang->tanggal_lunas = $request->tanggal;
            $piutang->jumlah_kembali = $piutang->jumlah; // cap at max
        }

        $piutang->save();

        return response()->json(['ok' => true, 'message' => 'Cicilan berhasil dicatat.']);
    }

    // Tandai macet
    public function tandaiMacet(Request $request)
    {
        $piutang = Piutang::findOrFail($request->id);
        $piutang->update(['status' => 'macet', 'keterangan' => $request->keterangan]);
        return response()->json(['ok' => true, 'message' => 'Piutang ditandai macet.']);
    }

    // Ringkasan
    public function ringkasan()
    {
        return [
            'total_piutang' => (float) Piutang::where('status', '!=', 'lunas')->sum('jumlah'),
            'total_kembali' => (float) Piutang::sum('jumlah_kembali'),
            'total_sisa'    => (float) Piutang::where('status', 'aktif')->get()->sum('sisa'),
            'lunas'         => Piutang::where('status', 'lunas')->count(),
            'aktif'         => Piutang::where('status', 'aktif')->count(),
            'macet'         => Piutang::where('status', 'macet')->count(),
        ];
    }
}
