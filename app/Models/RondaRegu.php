<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RondaRegu extends Model
{
    protected $table = 'ronda_regu';

    protected $fillable = ['nama', 'pos', 'keterangan'];

    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(Warga::class, 'ronda_anggota', 'ronda_regu_id', 'warga_id')
                    ->withTimestamps();
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(RondaJadwal::class, 'ronda_regu_id');
    }
}
