<?php

namespace App\Notifications;

use App\Models\IuranTagihan;
use Illuminate\Notifications\Notification;

class IuranTagihanBaruNotification extends Notification
{
    public function __construct(public IuranTagihan $tagihan) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'iuran',
            'label'   => 'Iuran',
            'judul'   => 'Tagihan iuran ' . $this->tagihan->periode->format('M Y') . ' telah diterbitkan',
            'penting' => true,
            'url'     => route('iuran-saya.index'),
            'waktu'   => null,
        ];
    }
}
