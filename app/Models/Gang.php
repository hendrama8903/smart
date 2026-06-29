<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gang extends Model
{
    protected $table = 'gang';

    protected $fillable = ['nama_gang', 'koordinator_id', 'keterangan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    // Koordinator yang ditugaskan di gang ini
    public function koordinator(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KoordinatorGang::class, 'gang_id');
    }

    public function kartuKeluarga(): HasMany
    {
        return $this->hasMany(KartuKeluarga::class, 'gang_id');
    }

    public function getJumlahKkAttribute(): int
    {
        return $this->kartuKeluarga()->count();
    }
}
