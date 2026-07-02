<?php

namespace App\Observers;

use App\Models\IuranPeriode;
use App\Models\IuranTagihan;
use App\Models\KartuKeluarga;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KartuKeluargaObserver
{
    /**
     * KK baru dibuat dan langsung aktif → buatkan tagihan untuk semua periode buka.
     */
    public function created(KartuKeluarga $kk): void
    {
        if ($kk->aktif) {
            $this->generateTagihanPeriodeBuka($kk);
        }
    }

    /**
     * KK diupdate → jika status aktif baru saja dinyalakan, buatkan tagihan.
     */
    public function updated(KartuKeluarga $kk): void
    {
        if ($kk->wasChanged('aktif') && $kk->aktif) {
            $this->generateTagihanPeriodeBuka($kk);
        }
    }

    private function generateTagihanPeriodeBuka(KartuKeluarga $kk): void
    {
        $openPeriodes = IuranPeriode::where('status', 'buka')
            ->with('jenisIuran')
            ->get();

        if ($openPeriodes->isEmpty()) return;

        $petugasId = Auth::id();
        $now       = now();

        foreach ($openPeriodes as $periode) {
            $periodeDate = $periode->bulan
                ? Carbon::create($periode->tahun, $periode->bulan, 1)->toDateString()
                : Carbon::create($periode->tahun, 1, 1)->toDateString();

            // firstOrCreate agar tidak duplikat jika dipanggil ulang
            IuranTagihan::firstOrCreate(
                [
                    'kartu_keluarga_id' => $kk->id,
                    'jenis_iuran_id'    => $periode->jenis_iuran_id,
                    'periode'           => $periodeDate,
                ],
                [
                    'periode_id'      => $periode->id,
                    'nominal'         => $periode->jenisIuran->nominal,
                    'nominal_dibayar' => 0,
                    'status'          => 'belum',
                    'petugas_id'      => $petugasId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]
            );
        }
    }
}
