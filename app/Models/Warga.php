<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warga extends Model
{
    use SoftDeletes;

    protected $table = 'warga';

    protected $fillable = [
        'kartu_keluarga_id', 'foto', 'nik', 'nama', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'pendidikan',
        'pekerjaan', 'status_perkawinan', 'hubungan',
        'no_telepon', 'status_tinggal', 'status_warga',
        'tgl_masuk', 'tgl_keluar', 'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tgl_masuk'     => 'date',
        'tgl_keluar'    => 'date',
    ];

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'warga_id');
    }

    public function rondaRegu(): BelongsToMany
    {
        return $this->belongsToMany(RondaRegu::class, 'ronda_anggota', 'warga_id', 'ronda_regu_id')
                    ->withTimestamps();
    }

    // Hitung umur dari tanggal lahir
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir?->age;
    }
}
