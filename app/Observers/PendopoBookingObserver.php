<?php

namespace App\Observers;

use App\Models\PendopoBooking;
use App\Models\User;
use App\Notifications\BookingPendingNotification;

class PendopoBookingObserver
{
    public function created(PendopoBooking $booking): void
    {
        if ($booking->status !== 'pending') {
            return;
        }

        User::where('status', 'aktif')
            ->whereHas('role', fn ($q) => $q->whereIn('nama', ['admin', 'ketua', 'sekretaris']))
            ->get()
            ->each(fn ($user) => $user->notify(new BookingPendingNotification($booking)));
    }
}
