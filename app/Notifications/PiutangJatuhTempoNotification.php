<?php

namespace App\Notifications;

use App\Models\Piutang;
use Illuminate\Notifications\Notification;

class PiutangJatuhTempoNotification extends Notification
{
    public function __construct(public Piutang $piutang) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $lewat = $this->piutang->jatuh_tempo->isPast();
        return [
            'type'    => 'piutang',
            'label'   => 'Piutang',
            'judul'   => ($lewat ? 'Piutang jatuh tempo: ' : 'Mendekati tempo: ') . $this->piutang->nama_peminjam,
            'penting' => $lewat,
            'url'     => route('piutang.index'),
            'waktu'   => $this->piutang->jatuh_tempo->format('d/m/Y'),
        ];
    }
}
