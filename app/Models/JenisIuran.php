<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisIuran extends Model
{
    protected $table = 'jenis_iuran';

    protected $fillable = ['nama', 'nominal', 'periode', 'keterangan', 'aktif'];

    protected $casts = [
        'nominal' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function tagihan(): HasMany
    {
        return $this->hasMany(IuranTagihan::class, 'jenis_iuran_id');
    }
}
