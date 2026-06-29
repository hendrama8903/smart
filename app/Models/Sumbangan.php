<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sumbangan extends Model
{
    protected $table = 'sumbangan';

    protected $fillable = [
        'penggalangan_id', 'kartu_keluarga_id', 'nama_donatur',
        'nominal', 'tanggal', 'metode', 'dicatat_oleh',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function penggalangan(): BelongsTo
    {
        return $this->belongsTo(Penggalangan::class, 'penggalangan_id');
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
