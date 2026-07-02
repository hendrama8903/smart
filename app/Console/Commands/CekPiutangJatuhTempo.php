<?php

namespace App\Console\Commands;

use App\Models\Piutang;
use App\Models\User;
use App\Notifications\PiutangJatuhTempoNotification;
use Illuminate\Console\Command;

class CekPiutangJatuhTempo extends Command
{
    protected $signature   = 'piutang:cek-tempo';
    protected $description = 'Kirim notifikasi piutang yang jatuh tempo dalam 7 hari';

    public function handle(): void
    {
        $piutangs = Piutang::whereNotIn('status', ['lunas'])
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', now()->addDays(7))
            ->get();

        if ($piutangs->isEmpty()) {
            $this->info('Tidak ada piutang mendekati jatuh tempo.');
            return;
        }

        $pengurus = User::where('status', 'aktif')
            ->whereHas('role', fn ($q) => $q->whereIn('nama', ['admin', 'ketua', 'bendahara']))
            ->get();

        foreach ($piutangs as $piutang) {
            // Hindari duplikat: skip jika notifikasi untuk piutang ini sudah dikirim hari ini
            $sudahAda = $pengurus->first()?->notifications()
                ->whereDate('created_at', today())
                ->where('data->judul', 'like', '%' . $piutang->nama_peminjam . '%')
                ->exists();

            if ($sudahAda) {
                continue;
            }

            $pengurus->each(fn ($user) => $user->notify(new PiutangJatuhTempoNotification($piutang)));
        }

        $this->info("Notifikasi dikirim untuk {$piutangs->count()} piutang.");
    }
}
