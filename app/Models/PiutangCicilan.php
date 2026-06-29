<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiutangCicilan extends Model
{
    protected $table = 'piutang_cicilan';

    protected $fillable = ['piutang_id', 'tanggal', 'jumlah', 'keterangan', 'bukti', 'dicatat_oleh'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'decimal:2',
    ];

    public function piutang(): BelongsTo
    {
        return $this->belongsTo(Piutang::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
