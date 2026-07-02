<?php

namespace App\Notifications;

use App\Models\PendopoBooking;
use Illuminate\Notifications\Notification;

class BookingPendingNotification extends Notification
{
    public function __construct(public PendopoBooking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'booking',
            'label'   => 'Booking',
            'judul'   => 'Booking "' . $this->booking->nama_acara . '" perlu persetujuan',
            'penting' => false,
            'url'     => route('booking.index'),
            'waktu'   => $this->booking->tanggal_mulai->format('d/m/Y'),
        ];
    }
}
