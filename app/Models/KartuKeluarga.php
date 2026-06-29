<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends Model
{
    protected $table = 'kartu_keluarga';

    protected $fillable = [
        'gang_id', 'no_kk', 'kepala_keluarga', 'blok', 'no_rumah', 'alamat',
        'rt', 'rw', 'no_telepon', 'status_tinggal', 'tgl_daftar',
        'keterangan', 'aktif',
    ];

    protected $casts = [
        'aktif'      => 'boolean',
        'tgl_daftar' => 'date',
    ];

    public function gang(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Gang::class, 'gang_id');
    }

    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class, 'kartu_keluarga_id');
    }

    public function iuranTagihan(): HasMany
    {
        return $this->hasMany(IuranTagihan::class, 'kartu_keluarga_id');
    }

    public function sumbangan(): HasMany
    {
        return $this->hasMany(Sumbangan::class, 'kartu_keluarga_id');
    }

    public function pendopoBooking(): HasMany
    {
        return $this->hasMany(PendopoBooking::class, 'kartu_keluarga_id');
    }

    // Label blok + no rumah
    public function getAlamatSingkatAttribute(): string
    {
        return trim(($this->blok ? $this->blok . ' ' : '') . ($this->no_rumah ?? ''));
    }
}
