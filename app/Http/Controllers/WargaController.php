<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    // ─── Halaman utama ───────────────────────────────────────────────────
    public function index()
    {
        return view('warga.index');
    }

    // ─── Data KK untuk grid utama ────────────────────────────────────────
    public function kkList()
    {
        return KartuKeluarga::withCount('warga as jumlah_jiwa')
            ->orderByRaw("blok IS NULL, blok")
            ->orderByRaw("no_rumah IS NULL, no_rumah + 0")
            ->get()
            ->map(fn ($kk) => [
                'id'            => $kk->id,
                'no_kk'         => $kk->no_kk,
                'kepala_keluarga' => $kk->kepala_keluarga,
                'blok'          => $kk->blok,
                'no_rumah'      => $kk->no_rumah,
                'alamat_singkat'=> $kk->alamat_singkat,
                'alamat'        => $kk->alamat,
                'rt'            => $kk->rt,
                'rw'            => $kk->rw,
                'no_telepon'    => $kk->no_telepon,
                'status_tinggal'=> $kk->status_tinggal,
                'tgl_daftar'    => $kk->tgl_daftar?->format('d/m/Y'),
                'keterangan'    => $kk->keterangan,
                'jumlah_jiwa'   => $kk->jumlah_jiwa,
                'aktif'         => $kk->aktif,
            ]);
    }

    // ─── Data warga untuk master-detail ──────────────────────────────────
    public function wargaList(KartuKeluarga $kk)
    {
        return $kk->warga()
            ->orderByRaw("FIELD(hubungan,'kepala_keluarga','istri','suami','anak','orang_tua','mertua','menantu','cucu','lainnya')")
            ->orderBy('nama')
            ->get()
            ->map(fn ($w) => [
                'id'               => $w->id,
                'kartu_keluarga_id'=> $w->kartu_keluarga_id,
                'foto_url'         => $w->foto ? asset('storage/' . $w->foto) : null,
                'nik'              => $w->nik,
                'nama'             => $w->nama,
                'jenis_kelamin'    => $w->jenis_kelamin,
                'tempat_lahir'     => $w->tempat_lahir,
                'tanggal_lahir'    => $w->tanggal_lahir?->format('d/m/Y'),
                'umur'             => $w->umur,
                'agama'            => $w->agama,
                'pendidikan'       => $w->pendidikan,
                'pekerjaan'        => $w->pekerjaan,
                'status_perkawinan'=> $w->status_perkawinan,
                'hubungan'         => $w->hubungan,
                'no_telepon'       => $w->no_telepon,
                'status_tinggal'   => $w->status_tinggal,
                'status_warga'     => $w->status_warga,
                'tgl_masuk'        => $w->tgl_masuk?->format('d/m/Y'),
                'tgl_keluar'       => $w->tgl_keluar?->format('d/m/Y'),
                'keterangan'       => $w->keterangan,
            ]);
    }

    // ─── Simpan KK ───────────────────────────────────────────────────────
    public function kkSave(Request $request)
    {
        $isEdit = $request->filled('id');

        $data = $request->validate([
            'no_kk'           => ['required', 'digits:16', Rule::unique('kartu_keluarga', 'no_kk')->ignore($request->id)],
            'kepala_keluarga' => ['required', 'string', 'max:100'],
            'nik_kepala'      => ['nullable', 'digits:16', Rule::unique('warga', 'nik')],
            'blok'            => ['nullable', 'string', 'max:20'],
            'no_rumah'        => ['nullable', 'string', 'max:10'],
            'alamat'          => ['nullable', 'string', 'max:255'],
            'rt'              => ['nullable', 'string', 'max:5'],
            'rw'              => ['nullable', 'string', 'max:5'],
            'no_telepon'      => ['nullable', 'string', 'max:20'],
            'status_tinggal'  => ['required', 'in:milik,sewa,numpang'],
            'tgl_daftar'      => ['nullable', 'date'],
            'keterangan'      => ['nullable', 'string'],
        ]);

        $nikKepala = $data['nik_kepala'] ?? null;
        unset($data['nik_kepala']); // tidak disimpan ke tabel KK

        $data['aktif'] = $request->boolean('aktif', true);

        if ($isEdit) {
            KartuKeluarga::findOrFail($request->id)->update($data);
            $msg = 'Kartu Keluarga berhasil diperbarui.';
        } else {
            $kk = KartuKeluarga::create($data);
            $msg = 'Kartu Keluarga berhasil ditambahkan.';

            // Auto-buat warga kepala keluarga jika NIK diisi
            if ($nikKepala) {
                // Mapping: status hunian KK → status tinggal warga
                $statusTinggalMap = ['milik' => 'tetap', 'sewa' => 'kontrak', 'numpang' => 'numpang'];
                $statusTinggal    = $statusTinggalMap[$data['status_tinggal']] ?? 'tetap';

                Warga::create([
                    'kartu_keluarga_id' => $kk->id,
                    'nik'               => $nikKepala,
                    'nama'              => $data['kepala_keluarga'],
                    'jenis_kelamin'     => 'L', // default, bisa diubah nanti
                    'hubungan'          => 'kepala_keluarga',
                    'status_tinggal'    => $statusTinggal,
                    'status_warga'      => 'aktif',
                    'no_telepon'        => $data['no_telepon'] ?? null,
                    'tgl_masuk'         => $data['tgl_daftar'] ?? null,
                ]);

                $msg .= ' Kepala keluarga otomatis ditambahkan ke data warga.';
            }
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Hapus KK ────────────────────────────────────────────────────────
    public function kkRemove(Request $request)
    {
        $kk = KartuKeluarga::withCount('warga as jumlah_jiwa')->findOrFail($request->id);

        if ($kk->jumlah_jiwa > 0) {
            return response()->json([
                'ok'      => false,
                'message' => 'Kartu Keluarga masih memiliki ' . $kk->jumlah_jiwa . ' anggota. Hapus anggota terlebih dahulu.',
            ], 422);
        }

        $kk->delete();
        return response()->json(['ok' => true, 'message' => 'Kartu Keluarga berhasil dihapus.']);
    }

    // ─── Simpan Warga ─────────────────────────────────────────────────────
    public function wargaSave(Request $request)
    {
        $isEdit = $request->filled('id');

        $data = $request->validate([
            'kartu_keluarga_id' => ['required', 'exists:kartu_keluarga,id'],
            'nik'               => ['required', 'digits:16', Rule::unique('warga', 'nik')->ignore($request->id)],
            'nama'              => ['required', 'string', 'max:100'],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'tempat_lahir'      => ['nullable', 'string', 'max:50'],
            'tanggal_lahir'     => ['nullable', 'date'],
            'agama'             => ['nullable', 'string', 'max:30'],
            'pendidikan'        => ['nullable', 'string', 'max:50'],
            'pekerjaan'         => ['nullable', 'string', 'max:100'],
            'status_perkawinan' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'hubungan'          => ['required', 'in:kepala_keluarga,istri,suami,anak,orang_tua,mertua,menantu,cucu,lainnya'],
            'no_telepon'        => ['nullable', 'string', 'max:20'],
            'status_tinggal'    => ['required', 'in:tetap,kontrak,kos,numpang'],  // status tinggal warga
            'status_warga'      => ['required', 'in:aktif,pindah,meninggal'],
            'tgl_masuk'         => ['nullable', 'date'],
            'tgl_keluar'        => ['nullable', 'date'],
            'keterangan'        => ['nullable', 'string'],
            'foto'              => ['nullable', 'string'],
        ]);

        if ($isEdit) {
            Warga::findOrFail($request->id)->update($data);
            $msg = 'Data warga berhasil diperbarui.';
        } else {
            Warga::create($data);
            $msg = 'Data warga berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Hapus Warga ──────────────────────────────────────────────────────
    // force=1 → hard delete (hapus permanen, admin only)
    // force=0 → soft delete (deleted_at diisi, data tersimpan untuk historis)
    public function wargaRemove(Request $request)
    {
        $warga = Warga::withTrashed()->findOrFail($request->id);
        $force = $request->boolean('force', false);

        if ($force) {
            // Hapus foto jika ada
            if ($warga->foto) {
                Storage::disk('public')->delete($warga->foto);
            }
            $warga->forceDelete();
            $msg = 'Data warga berhasil dihapus permanen.';
        } else {
            $warga->delete(); // soft delete
            $msg = 'Data warga berhasil dinonaktifkan (soft delete). Data masih tersimpan untuk historis.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    // ─── Upload Foto Warga ────────────────────────────────────────────────
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Hapus foto lama jika ada
        if ($request->filled('warga_id')) {
            $warga = Warga::find($request->warga_id);
            if ($warga && $warga->foto) {
                Storage::disk('public')->delete($warga->foto);
            }
        }

        $path = $request->file('foto')->store('warga/foto', 'public');

        return response()->json([
            'ok'   => true,
            'path' => $path,
            'url'  => asset('storage/' . $path),
        ]);
    }

    // ─── Import dari Excel ────────────────────────────────────────────────
    public function import(Request $request)
{
    $request->validate(['file' => ['required', 'file', 'mimes:xlsx,zip', 'max:5120']]);

    try {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(false);
        $sheet = $reader->load($request->file('file')->getRealPath())->getActiveSheet();

        $highestCol = $sheet->getHighestDataColumn();
        $highestRow = $sheet->getHighestDataRow();

        // Header
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator('A', $highestCol) as $cell) {
                $headers[] = strtolower(trim((string) $cell->getValue()));
            }
        }

        // 1) Baca SEMUA baris dulu jadi array asosiatif
        $rows = [];
        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $rowData = []; $colIdx = 0;
            foreach ($sheet->getRowIterator($rowNum, $rowNum) as $row) {
                foreach ($row->getCellIterator('A', $highestCol) as $cell) {
                    $val = $cell->getValue();
                    if (is_numeric($val) && $val > 10000 && isset($headers[$colIdx]) &&
                        (str_contains($headers[$colIdx], 'tgl') || str_contains($headers[$colIdx], 'tanggal'))) {
                        try {
                            $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                        } catch (\Throwable) {}
                    }
                    $rowData[$colIdx] = $val !== null ? trim((string) $val) : null;
                    $colIdx++;
                }
            }
            $rowData = array_pad($rowData, count($headers), null);
            $data = array_combine($headers, $rowData);

            if (blank($data['nik'] ?? null) && blank($data['nama'] ?? null)) continue; // skip baris kosong
            $data['_row'] = $rowNum;
            $rows[] = $data;
        }

        $errors = []; $successKk = 0; $successWarga = 0; $seenNik = [];

        DB::transaction(function () use ($rows, &$errors, &$successKk, &$successWarga, &$seenNik) {

            // 2) Kelompokkan per no_kk
            $grouped = collect($rows)->groupBy(fn ($r) => trim($r['no_kk'] ?? ''));

            foreach ($grouped as $noKk => $members) {
                if (! preg_match('/^\d{16}$/', $noKk)) {
                    foreach ($members as $m) $errors[] = "Baris {$m['_row']}: No. KK '{$noKk}' tidak valid (harus 16 digit).";
                    continue;
                }

                // Kepala keluarga = baris dgn hubungan kepala_keluarga, fallback baris pertama
                $kepala = $members->firstWhere('hubungan', 'kepala_keluarga') ?? $members->first();

                // 3) Buat / temukan KK SEKALI per no_kk
                $kk = KartuKeluarga::firstOrCreate(
                    ['no_kk' => $noKk],
                    [
                        'kepala_keluarga' => $kepala['nama'] ?? '-',
                        'no_telepon'      => $kepala['no_telepon'] ?? null,
                        'status_tinggal'  => 'milik',   // enum KK: milik/sewa/numpang (BUKAN nilai warga)
                        'aktif'           => true,
                        'tgl_daftar'      => $this->parseDate($kepala['tgl_masuk'] ?? null),
                    ]
                );
                if ($kk->wasRecentlyCreated) $successKk++;

                // 4) Masukkan tiap anggota
                foreach ($members as $data) {
                    $rowNum = $data['_row'];
                    $nik = trim($data['nik'] ?? '');

                    if (! preg_match('/^\d{16}$/', $nik)) {
                        $errors[] = "Baris {$rowNum}: NIK '{$nik}' tidak valid (harus 16 digit)."; continue;
                    }
                    if (isset($seenNik[$nik])) {                       // duplikat dalam file
                        $errors[] = "Baris {$rowNum}: NIK '{$nik}' duplikat di dalam file."; continue;
                    }
                    if (Warga::where('nik', $nik)->exists()) {         // duplikat di DB
                        $errors[] = "Baris {$rowNum}: NIK '{$nik}' sudah terdaftar."; continue;
                    }
                    $seenNik[$nik] = true;

                    $statusKawin   = in_array($data['status_perkawinan'] ?? '', ['belum_kawin','kawin','cerai_hidup','cerai_mati']) ? $data['status_perkawinan'] : 'belum_kawin';
                    $hubungan      = in_array($data['hubungan'] ?? '', ['kepala_keluarga','istri','suami','anak','orang_tua','mertua','menantu','cucu','lainnya']) ? $data['hubungan'] : 'lainnya';
                    $statusTinggal = in_array($data['status_tinggal'] ?? '', ['tetap','kontrak','kos','numpang']) ? $data['status_tinggal'] : 'tetap';
                    $statusWarga   = in_array($data['status_warga'] ?? '', ['aktif','pindah','meninggal']) ? $data['status_warga'] : 'aktif';

                    Warga::create([
                        'kartu_keluarga_id' => $kk->id,
                        'nik'               => $nik,
                        'nama'              => $data['nama'] ?? null,
                        'jenis_kelamin'     => strtoupper(trim($data['jenis_kelamin'] ?? '')) === 'P' ? 'P' : 'L',
                        'tempat_lahir'      => $data['tempat_lahir'] ?? null,
                        'tanggal_lahir'     => $this->parseDate($data['tanggal_lahir'] ?? null),
                        'agama'             => $data['agama'] ?? null,
                        'pendidikan'        => $data['pendidikan'] ?? null,
                        'pekerjaan'         => $data['pekerjaan'] ?? null,
                        'status_perkawinan' => $statusKawin,
                        'hubungan'          => $hubungan,
                        'no_telepon'        => $data['no_telepon'] ?? null,
                        'status_tinggal'    => $statusTinggal,
                        'status_warga'      => $statusWarga,
                        'tgl_masuk'         => $this->parseDate($data['tgl_masuk'] ?? null),
                        'keterangan'        => $data['keterangan'] ?? null,
                    ]);
                    $successWarga++;
                }
            }
        });

        $msg = "{$successKk} KK & {$successWarga} warga berhasil diimport.";
        if ($errors) $msg .= ' ' . count($errors) . ' baris dilewati.';

        return response()->json(['ok' => true, 'message' => $msg, 'errors' => $errors]);

    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'message' => 'Gagal memproses file: ' . $e->getMessage()], 422);
    }
}

    private function parseDate(?string $val): ?string
    {
        if (blank($val)) return null;
        // Format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;
        // Format d/m/Y atau d-m-Y
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
            return "{$m[3]}-" . str_pad($m[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }
        // Coba parse umum
        try {
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    // ─── Lookup KK untuk dropdown ─────────────────────────────────────────
    public function kkLookup(Request $request)
    {
        return KartuKeluarga::where('aktif', true)
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q2) use ($request) {
                $q2->where('no_kk', 'like', '%'.$request->q.'%')
                   ->orWhere('kepala_keluarga', 'like', '%'.$request->q.'%');
            }))
            ->orderBy('blok')->orderBy('no_rumah')
            ->limit(30)
            ->get(['id', 'no_kk', 'kepala_keluarga', 'blok', 'no_rumah']);
    }
}
