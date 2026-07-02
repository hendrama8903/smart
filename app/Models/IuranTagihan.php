<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuranTagihan extends Model
{
    protected $table = 'iuran_tagihan';

    protected $fillable = [
        'kartu_keluarga_id', 'jenis_iuran_id', 'periode_id', 'periode', 'nominal',
        'nominal_dibayar', 'status', 'tanggal_bayar', 'metode',
        'petugas_id', 'keterangan', 'bukti_bayar',
        'is_keringanan', 'catatan_khusus', 'is_historis', 'is_tunggakan',
    ];

    protected $casts = [
        'periode'         => 'date',
        'tanggal_bayar'   => 'date',
        'nominal'         => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'is_keringanan'   => 'boolean',
        'is_historis'     => 'boolean',
        'is_tunggakan'    => 'boolean',
    ];

    public function getSisaAttribute(): float
    {
        return max(0, (float) $this->nominal - (float) $this->nominal_dibayar);
    }

    public function updateStatus(): void
    {
        $dibayar = (float) $this->nominal_dibayar;
        $nominal = (float) $this->nominal;

        if ($dibayar <= 0) {
            $this->status = 'belum';
        } elseif ($dibayar >= $nominal) {
            $this->status = 'lunas';
        } else {
            $this->status = 'sebagian';
        }
        $this->save();
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function jenisIuran(): BelongsTo
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(IuranPeriode::class, 'periode_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function alokasi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IuranAlokasi::class, 'tagihan_id');
    }
}
