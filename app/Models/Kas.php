<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kas extends Model
{
    protected $table = 'kas';

    protected $fillable = [
        'tanggal', 'kategori_id', 'tipe', 'jumlah',
        'keterangan', 'ref_tabel', 'ref_id', 'dicatat_oleh', 'bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KasKategori::class, 'kategori_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
