<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggaran extends Model
{
    protected $table = 'anggaran';

    protected $fillable = ['tahun', 'kategori_id', 'tipe', 'nama_pos', 'nominal_rencana', 'keterangan'];

    protected $casts = ['nominal_rencana' => 'decimal:2'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KasKategori::class, 'kategori_id');
    }
}
