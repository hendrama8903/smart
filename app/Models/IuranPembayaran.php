<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranPembayaran extends Model
{
    protected $table = 'iuran_pembayaran';

    protected $fillable = [
        'kartu_keluarga_id', 'jenis_iuran_id', 'tanggal_bayar',
        'jumlah_total', 'metode', 'petugas_id', 'keterangan', 'bukti_bayar',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_total'  => 'decimal:2',
    ];

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function jenisIuran(): BelongsTo
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function alokasi(): HasMany
    {
        return $this->hasMany(IuranAlokasi::class, 'pembayaran_id');
    }
}
