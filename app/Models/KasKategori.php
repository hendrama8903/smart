<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasKategori extends Model
{
    protected $table = 'kas_kategori';

    protected $fillable = ['nama', 'tipe'];

    public function kas(): HasMany
    {
        return $this->hasMany(Kas::class, 'kategori_id');
    }
}
