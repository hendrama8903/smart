<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\KasKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KasController extends Controller
{
    public function index()
    {
        return view('keuangan.kas');
    }

    // ─── Data kas untuk grid ──────────────────────────────────────────────
    public function list(Request $request)
    {
        return Kas::with(['kategori', 'pencatat'])
            ->when($request->filled('tipe'), fn ($q) => $q->where('tipe', $request->tipe))
            ->when($request->filled('bulan'), fn ($q) => $q->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$request->bulan]))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($k) => [
                'id'         => $k->id,
                'tanggal'    => $k->tanggal->format('d/m/Y'),
                'tanggal_raw'=> $k->tanggal->format('Y-m-d'),
                'tipe'       => $k->tipe,
                'kategori_id'=> $k->kategori_id,
                'kategori'   => optional($k->kategori)->nama,
                'jumlah'     => (float) $k->jumlah,
                'keterangan' => $k->keterangan,
                'bukti_url'  => $k->bukti ? asset('storage/' . $k->bukti) : null,
                'pencatat'   => optional($k->pencatat)->name,
            ]);
    }

    // ─── Ringkasan saldo ──────────────────────────────────────────────────
    public function ringkasan(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));

        $masukBulan  = Kas::where('tipe', 'masuk')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$bulan])->sum('jumlah');
        $keluarBulan = Kas::where('tipe', 'keluar')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$bulan])->sum('jumlah');
        $totalMasuk  = Kas::where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = Kas::where('tipe', 'keluar')->sum('jumlah');

        return [
            'saldo_total'   => (float) ($totalMasuk - $totalKeluar),
            'masuk_bulan'   => (float) $masukBulan,
            'keluar_bulan'  => (float) $keluarBulan,
            'net_bulan'     => (float) ($masukBulan - $keluarBulan),
        ];
    }

    // ─── Kategori lookup ──────────────────────────────────────────────────
    public function kategoriList(Request $request)
    {
        return KasKategori::when($request->filled('tipe'), fn ($q) => $q->where('tipe', $request->tipe))
            ->orderBy('nama')
            ->get(['id', 'nama', 'tipe']);
    }

    public function kategoriSave(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'in:masuk,keluar'],
        ]);

        if ($request->filled('id')) {
            KasKategori::findOrFail($request->id)->update($data);
            $msg = 'Kategori berhasil diperbarui.';
        } else {
            KasKategori::create($data);
            $msg = 'Kategori berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Simpan transaksi kas ─────────────────────────────────────────────
    public function save(Request $request)
    {
        $data = $request->validate([
            'tanggal'    => ['required', 'date'],
            'tipe'       => ['required', 'in:masuk,keluar'],
            'kategori_id'=> ['required', 'exists:kas_kategori,id'],
            'jumlah'     => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $data['dicatat_oleh'] = auth()->id();

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('kas/bukti', 'public');
        }

        if ($request->filled('id')) {
            $kas = Kas::findOrFail($request->id);
            if ($request->hasFile('bukti') && $kas->bukti) {
                Storage::disk('public')->delete($kas->bukti);
            }
            $kas->update($data);
            $msg = 'Transaksi berhasil diperbarui.';
        } else {
            Kas::create($data);
            $msg = 'Transaksi berhasil disimpan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Hapus transaksi ──────────────────────────────────────────────────
    public function remove(Request $request)
    {
        $kas = Kas::findOrFail($request->id);
        if ($kas->bukti) Storage::disk('public')->delete($kas->bukti);
        $kas->delete();
        return response()->json(['ok' => true, 'message' => 'Transaksi berhasil dihapus.']);
    }

    // ─── Laporan cashflow bulanan ─────────────────────────────────────────
    public function cashflowBulanan(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $data = Kas::selectRaw("DATE_FORMAT(tanggal,'%Y-%m') as bulan, tipe, SUM(jumlah) as total")
            ->whereRaw("YEAR(tanggal) = ?", [$tahun])
            ->groupBy('bulan', 'tipe')
            ->get();

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $key    = $tahun . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $masuk  = $data->where('bulan', $key)->where('tipe', 'masuk')->first()?->total ?? 0;
            $keluar = $data->where('bulan', $key)->where('tipe', 'keluar')->first()?->total ?? 0;
            $result[] = [
                'bulan'  => $key,
                'label'  => \Carbon\Carbon::create($tahun, $m)->locale('id')->isoFormat('MMM'),
                'masuk'  => (float) $masuk,
                'keluar' => (float) $keluar,
                'net'    => (float) ($masuk - $keluar),
            ];
        }

        return $result;
    }

    // ─── Upload bukti pengeluaran ─────────────────────────────────────────
    public function uploadBukti(Request $request)
    {
        $request->validate(['bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']]);
        $path = $request->file('bukti')->store('kas/bukti', 'public');
        return response()->json(['ok' => true, 'path' => $path, 'url' => asset('storage/' . $path)]);
    }
}
