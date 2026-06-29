<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RondaJadwal extends Model
{
    protected $table = 'ronda_jadwal';

    protected $fillable = ['ronda_regu_id', 'tanggal', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function regu(): BelongsTo
    {
        return $this->belongsTo(RondaRegu::class, 'ronda_regu_id');
    }
}
