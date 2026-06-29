<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanWargaController extends Controller
{
    // ─── Halaman navigasi laporan warga ──────────────────────────────────
    public function index()
    {
        return view('laporan.warga-index');
    }

    // ─── 1. Demografi ─────────────────────────────────────────────────────
    public function demografi()
    {
        return view('laporan.warga-demografi');
    }

    public function demografiData(Request $request)
    {
        $gangId = $request->input('gang_id');

        $base = Warga::where('status_warga', 'aktif')
            ->when($gangId, fn ($q) => $q->whereHas('kartuKeluarga', fn ($q2) => $q2->where('gang_id', $gangId)));

        // Jenis Kelamin
        $gender = (clone $base)->groupBy('jenis_kelamin')
            ->select('jenis_kelamin', DB::raw('COUNT(*) as jumlah'))
            ->pluck('jumlah', 'jenis_kelamin');

        // Kelompok Usia
        $now  = now();
        $usia = [
            'Balita (0-4)'    => (clone $base)->whereNotNull('tanggal_lahir')->whereRaw("TIMESTAMPDIFF(YEAR,tanggal_lahir,NOW()) BETWEEN 0 AND 4")->count(),
            'Anak (5-12)'     => (clone $base)->whereNotNull('tanggal_lahir')->whereRaw("TIMESTAMPDIFF(YEAR,tanggal_lahir,NOW()) BETWEEN 5 AND 12")->count(),
            'Remaja (13-17)'  => (clone $base)->whereNotNull('tanggal_lahir')->whereRaw("TIMESTAMPDIFF(YEAR,tanggal_lahir,NOW()) BETWEEN 13 AND 17")->count(),
            'Dewasa (18-59)'  => (clone $base)->whereNotNull('tanggal_lahir')->whereRaw("TIMESTAMPDIFF(YEAR,tanggal_lahir,NOW()) BETWEEN 18 AND 59")->count(),
            'Lansia (60+)'    => (clone $base)->whereNotNull('tanggal_lahir')->whereRaw("TIMESTAMPDIFF(YEAR,tanggal_lahir,NOW()) >= 60")->count(),
            'Tidak diketahui' => (clone $base)->whereNull('tanggal_lahir')->count(),
        ];

        // Agama
        $agama = (clone $base)->whereNotNull('agama')
            ->groupBy('agama')->select('agama', DB::raw('COUNT(*) as jumlah'))
            ->orderByDesc('jumlah')->get()->pluck('jumlah', 'agama');

        // Pendidikan
        $pendOrder = ['Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'];
        $pendidikan = (clone $base)->whereNotNull('pendidikan')
            ->groupBy('pendidikan')->select('pendidikan', DB::raw('COUNT(*) as jumlah'))
            ->get()->pluck('jumlah', 'pendidikan');

        // Pekerjaan top 10
        $pekerjaan = (clone $base)->whereNotNull('pekerjaan')
            ->groupBy('pekerjaan')->select('pekerjaan', DB::raw('COUNT(*) as jumlah'))
            ->orderByDesc('jumlah')->limit(10)->get();

        // Status tinggal
        $statusTinggal = (clone $base)
            ->groupBy('status_tinggal')->select('status_tinggal', DB::raw('COUNT(*) as jumlah'))
            ->get()->pluck('jumlah', 'status_tinggal');

        // Hubungan
        $hubungan = (clone $base)
            ->groupBy('hubungan')->select('hubungan', DB::raw('COUNT(*) as jumlah'))
            ->orderByDesc('jumlah')->get()->pluck('jumlah', 'hubungan');

        // Summary KK
        $totalKK    = KartuKeluarga::where('aktif', true)->when($gangId, fn ($q) => $q->where('gang_id', $gangId))->count();
        $totalWarga = (clone $base)->count();

        return response()->json([
            'total_warga'    => $totalWarga,
            'total_kk'       => $totalKK,
            'gender'         => $gender,
            'usia'           => $usia,
            'agama'          => $agama,
            'pendidikan'     => $pendidikan,
            'pekerjaan'      => $pekerjaan,
            'status_tinggal' => $statusTinggal,
            'hubungan'       => $hubungan,
        ]);
    }

    // ─── 2. Daftar Warga Lengkap ──────────────────────────────────────────
    public function daftar()
    {
        return view('laporan.warga-daftar');
    }

    public function daftarData(Request $request)
    {
        $gangId       = $request->input('gang_id');
        $statusWarga  = $request->input('status_warga', 'aktif');
        $statusTinggal= $request->input('status_tinggal');

        return Warga::with(['kartuKeluarga.gang'])
            ->when($statusWarga, fn ($q) => $q->where('status_warga', $statusWarga))
            ->when($statusTinggal, fn ($q) => $q->where('status_tinggal', $statusTinggal))
            ->when($gangId, fn ($q) => $q->whereHas('kartuKeluarga', fn ($q2) => $q2->where('gang_id', $gangId)))
            ->orderByRaw("FIELD(hubungan,'kepala_keluarga','istri','suami','anak','lainnya')")
            ->orderBy('nama')
            ->get()
            ->map(fn ($w) => [
                'nik'               => $w->nik,
                'nama'              => $w->nama,
                'jenis_kelamin'     => $w->jenis_kelamin,
                'umur'              => $w->umur,
                'tempat_lahir'      => $w->tempat_lahir,
                'tanggal_lahir'     => $w->tanggal_lahir?->format('d/m/Y'),
                'agama'             => $w->agama,
                'pendidikan'        => $w->pendidikan,
                'pekerjaan'         => $w->pekerjaan,
                'status_perkawinan' => $w->status_perkawinan,
                'hubungan'          => $w->hubungan,
                'no_telepon'        => $w->no_telepon,
                'status_tinggal'    => $w->status_tinggal,
                'status_warga'      => $w->status_warga,
                'gang'              => optional(optional($w->kartuKeluarga)->gang)->nama_gang,
                'blok_no'           => optional($w->kartuKeluarga)->alamat_singkat,
                'kepala_kk'         => optional($w->kartuKeluarga)->kepala_keluarga,
                'no_kk'             => optional($w->kartuKeluarga)->no_kk,
            ]);
    }

    // ─── 3. Mutasi Warga ──────────────────────────────────────────────────
    public function mutasi()
    {
        return view('laporan.warga-mutasi');
    }

    public function mutasiData(Request $request)
    {
        $tahun  = (int) $request->input('tahun', now()->year);
        $bulan  = $request->input('bulan'); // null = semua bulan

        // Warga baru masuk (tgl_masuk di tahun/bulan ini)
        $masuk = Warga::with(['kartuKeluarga.gang'])
            ->whereNotNull('tgl_masuk')
            ->whereRaw("YEAR(tgl_masuk) = ?", [$tahun])
            ->when($bulan, fn ($q) => $q->whereRaw("MONTH(tgl_masuk) = ?", [$bulan]))
            ->orderBy('tgl_masuk', 'desc')
            ->get()->map(fn ($w) => $this->wargaRow($w, 'masuk'));

        // Warga pindah (status_warga = pindah, tgl_keluar di periode ini)
        $pindah = Warga::withTrashed()->with(['kartuKeluarga.gang'])
            ->where('status_warga', 'pindah')
            ->whereNotNull('tgl_keluar')
            ->whereRaw("YEAR(tgl_keluar) = ?", [$tahun])
            ->when($bulan, fn ($q) => $q->whereRaw("MONTH(tgl_keluar) = ?", [$bulan]))
            ->orderBy('tgl_keluar', 'desc')
            ->get()->map(fn ($w) => $this->wargaRow($w, 'pindah'));

        // Warga meninggal
        $meninggal = Warga::withTrashed()->with(['kartuKeluarga.gang'])
            ->where('status_warga', 'meninggal')
            ->whereNotNull('tgl_keluar')
            ->whereRaw("YEAR(tgl_keluar) = ?", [$tahun])
            ->when($bulan, fn ($q) => $q->whereRaw("MONTH(tgl_keluar) = ?", [$bulan]))
            ->orderBy('tgl_keluar', 'desc')
            ->get()->map(fn ($w) => $this->wargaRow($w, 'meninggal'));

        // Summary per bulan
        $perBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $perBulan[] = [
                'bulan'     => $m,
                'label'     => \Carbon\Carbon::create($tahun, $m)->locale('id')->isoFormat('MMM'),
                'masuk'     => Warga::whereRaw("YEAR(tgl_masuk)=? AND MONTH(tgl_masuk)=?", [$tahun,$m])->count(),
                'pindah'    => Warga::withTrashed()->where('status_warga','pindah')->whereRaw("YEAR(tgl_keluar)=? AND MONTH(tgl_keluar)=?",[$tahun,$m])->count(),
                'meninggal' => Warga::withTrashed()->where('status_warga','meninggal')->whereRaw("YEAR(tgl_keluar)=? AND MONTH(tgl_keluar)=?",[$tahun,$m])->count(),
            ];
        }

        return response()->json([
            'masuk'     => $masuk->values(),
            'pindah'    => $pindah->values(),
            'meninggal' => $meninggal->values(),
            'per_bulan' => $perBulan,
        ]);
    }

    private function wargaRow(Warga $w, string $jenis): array
    {
        return [
            'nama'        => $w->nama,
            'nik'         => $w->nik,
            'jenis'       => $jenis,
            'hubungan'    => $w->hubungan,
            'umur'        => $w->umur,
            'tgl_masuk'   => $w->tgl_masuk?->format('d/m/Y'),
            'tgl_keluar'  => $w->tgl_keluar?->format('d/m/Y'),
            'keterangan'  => $w->keterangan,
            'gang'        => optional(optional($w->kartuKeluarga)->gang)->nama_gang,
            'blok_no'     => optional($w->kartuKeluarga)->alamat_singkat,
            'kepala_kk'   => optional($w->kartuKeluarga)->kepala_keluarga,
        ];
    }
}
