<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'kategori', 'isi', 'tanggal',
        'file_lampiran', 'nama_file', 'penting', 'aktif', 'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'penting' => 'boolean',
        'aktif'   => 'boolean',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_lampiran ? asset('storage/' . $this->file_lampiran) : null;
    }

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'rapat'     => 'Hasil Rapat',
            'kegiatan'  => 'Kegiatan',
            'keuangan'  => 'Keuangan',
            'darurat'   => 'Darurat',
            'lainnya'   => 'Lainnya',
            default     => 'Informasi',
        };
    }
}
