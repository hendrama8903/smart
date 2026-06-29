<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KoordinatorGang extends Model
{
    protected $table = 'koordinator_gang';

    protected $fillable = ['warga_id', 'gang_id', 'keterangan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function gang(): BelongsTo
    {
        return $this->belongsTo(Gang::class, 'gang_id');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(KoordinatorAnggota::class, 'koordinator_id');
    }

    // Nama lengkap warga (shortcut)
    public function getNamaAttribute(): string
    {
        return optional($this->warga)->nama ?? '—';
    }
}
