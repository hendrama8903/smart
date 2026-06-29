<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piutang extends Model
{
    protected $table = 'piutang';

    protected $fillable = [
        'kartu_keluarga_id', 'nama_peminjam', 'jumlah', 'jumlah_kembali',
        'tanggal_pinjam', 'jatuh_tempo', 'tanggal_lunas',
        'status', 'keterangan', 'bukti', 'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'jatuh_tempo'    => 'date',
        'tanggal_lunas'  => 'date',
        'jumlah'         => 'decimal:2',
        'jumlah_kembali' => 'decimal:2',
    ];

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function cicilan(): HasMany
    {
        return $this->hasMany(PiutangCicilan::class);
    }

    public function getSisaAttribute(): float
    {
        return max(0, (float) $this->jumlah - (float) $this->jumlah_kembali);
    }
}
