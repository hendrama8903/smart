<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama', 'deskripsi', 'satuan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function tarif(): HasMany
    {
        return $this->hasMany(TarifFasilitas::class, 'fasilitas_id');
    }

    public function booking(): HasMany
    {
        return $this->hasMany(PendopoBooking::class, 'fasilitas_id');
    }
}
