<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranPeriode extends Model
{
    protected $table = 'iuran_periode';

    protected $fillable = [
        'jenis_iuran_id', 'tahun', 'bulan', 'status',
        'tanggal_buka', 'dibuka_oleh',
        'tanggal_tutup', 'ditutup_oleh',
        'snap_total_tagihan', 'snap_total_terkumpul', 'snap_total_tunggakan',
        'catatan_penutupan',
    ];

    protected $casts = [
        'tanggal_buka'          => 'date',
        'tanggal_tutup'         => 'date',
        'snap_total_tagihan'    => 'decimal:2',
        'snap_total_terkumpul'  => 'decimal:2',
        'snap_total_tunggakan'  => 'decimal:2',
    ];

    public function getLabelAttribute(): string
    {
        $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];
        $bulan = $this->bulan ? ($namaBulan[$this->bulan] ?? '') : null;
        return $bulan ? "{$bulan} {$this->tahun}" : "Tahun {$this->tahun}";
    }

    public function jenisIuran(): BelongsTo
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id');
    }

    public function dibukaDari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function ditutupOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditutup_oleh');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(IuranTagihan::class, 'periode_id');
    }
}
