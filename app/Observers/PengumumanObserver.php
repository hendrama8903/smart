<?php

namespace App\Observers;

use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\PengumumanBaruNotification;

class PengumumanObserver
{
    public function created(Pengumuman $pengumuman): void
    {
        if (! $pengumuman->aktif) {
            return;
        }

        User::where('status', 'aktif')
            ->get()
            ->each(fn ($user) => $user->notify(new PengumumanBaruNotification($pengumuman)));
    }

    public function updated(Pengumuman $pengumuman): void
    {
        // Baru diaktifkan → kirim notifikasi
        if ($pengumuman->aktif && $pengumuman->wasChanged('aktif')) {
            User::where('status', 'aktif')
                ->get()
                ->each(fn ($user) => $user->notify(new PengumumanBaruNotification($pengumuman)));
        }
    }
}
