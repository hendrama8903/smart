<?php

namespace App\Notifications;

use App\Models\Pengumuman;
use Illuminate\Notifications\Notification;

class PengumumanBaruNotification extends Notification
{
    public function __construct(public Pengumuman $pengumuman) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'pengumuman',
            'label'   => 'Pengumuman',
            'judul'   => $this->pengumuman->judul,
            'penting' => $this->pengumuman->penting,
            'url'     => route('pengumuman.publik'),
        ];
    }
}
