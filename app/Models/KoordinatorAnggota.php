<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoordinatorAnggota extends Model
{
    protected $table = 'koordinator_anggota';

    protected $fillable = ['koordinator_id', 'kartu_keluarga_id'];

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(KoordinatorGang::class, 'koordinator_id');
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(\App\Models\KartuKeluarga::class, 'kartu_keluarga_id');
    }
}
