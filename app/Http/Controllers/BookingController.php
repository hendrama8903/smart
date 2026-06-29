<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\KasKategori;
use App\Models\PendopoBooking;
use App\Models\TarifFasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index()
    {
        return view('keuangan.booking');
    }

    public function list(Request $request)
    {
        return PendopoBooking::with(['fasilitas', 'tarifFasilitas', 'kartuKeluarga', 'penyetuju'])
            ->when($request->filled('bulan'), fn ($q) =>
                $q->whereRaw("DATE_FORMAT(tanggal_mulai,'%Y-%m') = ?", [$request->bulan]))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->map(fn ($b) => [
                'id'               => $b->id,
                'fasilitas_id'     => $b->fasilitas_id,
                'fasilitas'        => optional($b->fasilitas)->nama,
                'tarif_fasilitas_id' => $b->tarif_fasilitas_id,
                'nama_tarif'       => optional($b->tarifFasilitas)->nama_tarif,
                'kategori'         => optional($b->tarifFasilitas)->kategori,
                'kartu_keluarga_id'=> $b->kartu_keluarga_id,
                'nama_pemohon'     => $b->nama_pemohon,
                'nama_acara'       => $b->nama_acara,
                'tanggal_mulai'    => $b->tanggal_mulai?->format('d/m/Y'),
                'tanggal_selesai'  => $b->tanggal_selesai?->format('d/m/Y'),
                'jam_mulai'        => $b->jam_mulai,
                'jam_selesai'      => $b->jam_selesai,
                'is_warga'         => $b->is_warga,
                'jumlah_unit'      => (float) $b->jumlah_unit,
                'total_bayar'      => (float) $b->total_bayar,
                'total_kas_rt'     => (float) $b->total_kas_rt,
                'total_biaya_lain' => (float) $b->total_biaya_lain,
                'status'           => $b->status,
                'status_bayar'     => $b->status_bayar,
                'tgl_bayar'        => $b->tgl_bayar?->format('d/m/Y'),
                'keterangan'       => $b->keterangan,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'fasilitas_id'      => ['required', 'exists:fasilitas,id'],
            'tarif_fasilitas_id'=> ['required', 'exists:tarif_fasilitas,id'],
            'kartu_keluarga_id' => ['nullable', 'exists:kartu_keluarga,id'],
            'nama_pemohon'      => ['required', 'string', 'max:100'],
            'nama_acara'        => ['required', 'string', 'max:150'],
            'tanggal_mulai'     => ['required', 'date'],
            'tanggal_selesai'   => ['nullable', 'date'],
            'jam_mulai'         => ['nullable', 'string', 'max:10'],
            'jam_selesai'       => ['nullable', 'string', 'max:10'],
            'jumlah_unit'       => ['required', 'numeric', 'min:1'],
            'keterangan'        => ['nullable', 'string'],
        ]);

        // Hitung total dari tarif × jumlah unit
        $tarif = TarifFasilitas::findOrFail($request->tarif_fasilitas_id);
        $unit  = (float) $request->jumlah_unit;

        $data['is_warga']          = $tarif->kategori === 'warga';
        $data['total_bayar']       = $tarif->nominal_total  * $unit;
        $data['total_kas_rt']      = $tarif->nominal_kas_rt * $unit;
        $data['total_biaya_lain']  = $tarif->nominal_lain   * $unit;
        $data['tarif']             = $tarif->nominal_total;
        $data['status']            = $request->status ?? 'menunggu';
        $data['status_bayar']      = 'belum';
        $data['kartu_keluarga_id'] = $request->kartu_keluarga_id ?: null;

        if ($request->filled('id')) {
            PendopoBooking::findOrFail($request->id)->update($data);
            $msg = 'Booking berhasil diperbarui.';
        } else {
            PendopoBooking::create($data);
            $msg = 'Booking berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $b = PendopoBooking::findOrFail($request->id);
        if ($b->bukti_bayar) Storage::disk('public')->delete($b->bukti_bayar);
        $b->delete();
        return response()->json(['ok' => true, 'message' => 'Booking berhasil dihapus.']);
    }

    // ─── Setujui / Tolak booking ──────────────────────────────────────
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => ['required', 'exists:pendopo_booking,id'],
            'status' => ['required', 'in:menunggu,disetujui,ditolak,selesai'],
        ]);

        $booking = PendopoBooking::findOrFail($request->id);
        $booking->update([
            'status'         => $request->status,
            'disetujui_oleh' => in_array($request->status, ['disetujui', 'ditolak']) ? auth()->id() : $booking->disetujui_oleh,
        ]);

        return response()->json(['ok' => true, 'message' => 'Status booking diperbarui.']);
    }

    // ─── Catat pembayaran ─────────────────────────────────────────────
    public function bayar(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'exists:pendopo_booking,id'],
            'status_bayar'=> ['required', 'in:dp,lunas'],
            'tgl_bayar'   => ['required', 'date'],
        ]);

        $booking = PendopoBooking::findOrFail($request->id);
        $booking->update([
            'status_bayar' => $request->status_bayar,
            'tgl_bayar'    => $request->tgl_bayar,
        ]);

        // Otomatis catat ke Kas RT jika lunas
        if ($request->status_bayar === 'lunas' && $booking->total_kas_rt > 0) {
            $kategori = KasKategori::where('nama', 'Sewa Pendopo')->first();
            Kas::updateOrCreate(
                ['ref_tabel' => 'pendopo_booking', 'ref_id' => $booking->id],
                [
                    'tanggal'     => $request->tgl_bayar,
                    'kategori_id' => $kategori?->id,
                    'tipe'        => 'masuk',
                    'jumlah'      => $booking->total_kas_rt,
                    'keterangan'  => 'Sewa ' . optional($booking->fasilitas)->nama . ' — ' . $booking->nama_acara . ' (' . $booking->nama_pemohon . ')',
                    'dicatat_oleh'=> auth()->id(),
                ]
            );
        }

        return response()->json(['ok' => true, 'message' => 'Pembayaran berhasil dicatat.']);
    }

    // ─── Ringkasan bulan ini ──────────────────────────────────────────
    public function ringkasan(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $q = PendopoBooking::whereRaw("DATE_FORMAT(tanggal_mulai,'%Y-%m') = ?", [$bulan]);

        return [
            'total'       => (clone $q)->count(),
            'menunggu'    => (clone $q)->where('status', 'menunggu')->count(),
            'disetujui'   => (clone $q)->where('status', 'disetujui')->count(),
            'total_kas_rt'=> (float) (clone $q)->where('status_bayar', 'lunas')->sum('total_kas_rt'),
        ];
    }
}
