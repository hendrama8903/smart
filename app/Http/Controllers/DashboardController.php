<?php

namespace App\Http\Controllers;

use App\Models\IuranTagihan;
use App\Models\KartuKeluarga;
use App\Models\Kas;
use App\Models\PendopoBooking;
use App\Models\Pengumuman;
use App\Models\Piutang;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $isPengurus = $user->hasRole('admin', 'ketua', 'sekretaris', 'bendahara');

        $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];

        $tahunInput = (int) $request->input('tahun', now()->year);
        $bulanInput = (int) $request->input('bulan', now()->month);
        if ($tahunInput < 2000 || $tahunInput > 2100) $tahunInput = now()->year;
        if ($bulanInput < 1   || $bulanInput > 12)   $bulanInput = now()->month;

        $periodeInput = sprintf('%04d-%02d', $tahunInput, $bulanInput);
        $periode      = $periodeInput . '-01';
        $bulan        = ($namaBulan[$bulanInput] ?? '') . ' ' . $tahunInput;

        $tahunOptions = array_map(fn ($y) => ['value' => $y, 'label' => (string)$y],
                            range(now()->year - 3, now()->year + 1));
        $bulanOptions = array_map(fn ($b) => ['value' => $b, 'label' => $namaBulan[$b]],
                            range(1, 12));

        $saldoKas    = (float) Kas::where('tipe','masuk')->sum('jumlah')
                     - (float) Kas::where('tipe','keluar')->sum('jumlah');
        $kasmasukBulan  = (float) Kas::where('tipe','masuk')
            ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$periodeInput])->sum('jumlah');
        $kaskeluarBulan = (float) Kas::where('tipe','keluar')
            ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$periodeInput])->sum('jumlah');

        $jumlahWarga = Warga::where('status_warga','aktif')->count();
        $jumlahKK    = KartuKeluarga::where('aktif', true)->count();

        $tunggakanKK      = IuranTagihan::where('periode', $periode)
            ->whereIn('status', ['belum','sebagian'])->count();
        $tunggakanNominal = (float) IuranTagihan::where('periode', $periode)
            ->whereIn('status', ['belum','sebagian'])->get()->sum(fn ($t) => $t->sisa);

        $bookingMendatang  = PendopoBooking::where('status','disetujui')
            ->where('tanggal_mulai', '>=', now()->toDateString())->count();
        $bookingBerikutnya = PendopoBooking::with('fasilitas')
            ->where('status','disetujui')
            ->where('tanggal_mulai', '>=', now()->toDateString())
            ->orderBy('tanggal_mulai')->first();

        $piutangAktif = Piutang::where('status','aktif')->count();
        $piutangTotal = (float) Piutang::where('status','aktif')->get()->sum('sisa');

        // ─── Pengumuman terbaru ──────────────────────────────────────────
        $pengumumanTerbaru = Pengumuman::with('pembuat')
            ->where('aktif', true)
            ->orderBy('penting', 'desc')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(4)->get();

        $iuranTerbaru = IuranTagihan::with(['kartuKeluarga','jenisIuran'])
            ->whereNotNull('tanggal_bayar')
            ->whereRaw("DATE_FORMAT(tanggal_bayar,'%Y-%m') = ?", [$periodeInput])
            ->orderBy('tanggal_bayar','desc')->orderBy('id','desc')
            ->limit(5)->get();

        $kasTerbaru = Kas::with('kategori')
            ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = ?", [$periodeInput])
            ->orderBy('tanggal','desc')->orderBy('id','desc')
            ->limit(5)->get();

        $cashflow = [];
        for ($i = 5; $i >= 0; $i--) {
            $b      = \Carbon\Carbon::parse($periode)->subMonths($i)->format('Y-m');
            $label  = \Carbon\Carbon::parse($b)->locale('id')->isoFormat('MMM');
            $masuk  = (float) Kas::where('tipe','masuk')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?",[$b])->sum('jumlah');
            $keluar = (float) Kas::where('tipe','keluar')->whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?",[$b])->sum('jumlah');
            $cashflow[] = ['label' => $label, 'bulan' => $b, 'masuk' => $masuk, 'keluar' => $keluar];
        }

        $iuranBulan = IuranTagihan::where('periode', $periode)
            ->selectRaw("status, COUNT(*) as jumlah, SUM(nominal) as total, SUM(nominal_dibayar) as dibayar")
            ->groupBy('status')->get()->keyBy('status');

        return view('dashboard', compact(
            'isPengurus','user','bulan','periodeInput',
            'tahunInput','bulanInput','tahunOptions','bulanOptions',
            'pengumumanTerbaru',
            'saldoKas','kasmasukBulan','kaskeluarBulan',
            'jumlahWarga','jumlahKK',
            'tunggakanKK','tunggakanNominal',
            'bookingMendatang','bookingBerikutnya',
            'piutangAktif','piutangTotal',
            'iuranTerbaru','kasTerbaru','cashflow','iuranBulan'
        ));
    }
}
