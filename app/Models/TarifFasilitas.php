<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifFasilitas extends Model
{
    protected $table = 'tarif_fasilitas';

    protected $fillable = [
        'fasilitas_id', 'nama_tarif', 'kategori',
        'nominal_total', 'nominal_kas_rt', 'nominal_lain',
        'keterangan_lain', 'aktif',
    ];

    protected $casts = [
        'nominal_total'  => 'decimal:2',
        'nominal_kas_rt' => 'decimal:2',
        'nominal_lain'   => 'decimal:2',
        'aktif'          => 'boolean',
    ];

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }
}
