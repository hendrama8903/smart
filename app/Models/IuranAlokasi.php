<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuranAlokasi extends Model
{
    protected $table = 'iuran_alokasi';

    protected $fillable = ['pembayaran_id', 'tagihan_id', 'jumlah'];

    protected $casts = ['jumlah' => 'decimal:2'];

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(IuranPembayaran::class, 'pembayaran_id');
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(IuranTagihan::class, 'tagihan_id');
    }
}
