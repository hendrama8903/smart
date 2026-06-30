<?php

namespace App\Observers;

use App\Models\IuranTagihan;
use App\Models\User;
use App\Notifications\IuranTagihanBaruNotification;

class IuranTagihanObserver
{
    public function created(IuranTagihan $tagihan): void
    {
        // Hanya kirim notifikasi ke warga pemilik KK
        $user = User::where('status', 'aktif')
            ->whereHas('warga', fn ($q) => $q->where('kartu_keluarga_id', $tagihan->kartu_keluarga_id))
            ->first();

        $user?->notify(new IuranTagihanBaruNotification($tagihan));
    }
}
