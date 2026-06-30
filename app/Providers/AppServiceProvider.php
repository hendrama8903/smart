<?php

namespace App\Providers;

use App\Models\IuranTagihan;
use App\Models\KartuKeluarga;
use App\Models\Kas;
use App\Models\PendopoBooking;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Warga;
use App\Observers\AuditObserver;
use App\Observers\IuranTagihanObserver;
use App\Observers\PendopoBookingObserver;
use App\Observers\PengumumanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Warga::observe(AuditObserver::class);
        KartuKeluarga::observe(AuditObserver::class);
        Kas::observe(AuditObserver::class);
        IuranTagihan::observe(AuditObserver::class);
        Pengumuman::observe(AuditObserver::class);
        User::observe(AuditObserver::class);

        // Notifikasi
        Pengumuman::observe(PengumumanObserver::class);
        PendopoBooking::observe(PendopoBookingObserver::class);
        IuranTagihan::observe(IuranTagihanObserver::class);
    }
}
