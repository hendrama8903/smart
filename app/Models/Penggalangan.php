<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penggalangan extends Model
{
    protected $table = 'penggalangan';

    protected $fillable = [
        'judul', 'deskripsi', 'target', 'tanggal_mulai',
        'tanggal_selesai', 'status', 'dibuat_oleh',
    ];

    protected $casts = [
        'target' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function sumbangan(): HasMany
    {
        return $this->hasMany(Sumbangan::class, 'penggalangan_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
