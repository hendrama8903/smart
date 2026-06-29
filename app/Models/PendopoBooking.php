<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendopoBooking extends Model
{
    protected $table = 'pendopo_booking';

    protected $fillable = [
        'kartu_keluarga_id', 'tarif_fasilitas_id', 'fasilitas_id',
        'nama_pemohon', 'nama_acara', 'tanggal_mulai', 'tanggal_selesai',
        'jam_mulai', 'jam_selesai', 'is_warga', 'jumlah_unit',
        'total_bayar', 'total_kas_rt', 'total_biaya_lain',
        'tarif', 'deposit', 'status', 'status_bayar',
        'tgl_bayar', 'bukti_bayar', 'disetujui_oleh', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'    => 'date',
        'tanggal_selesai'  => 'date',
        'tgl_bayar'        => 'date',
        'is_warga'         => 'boolean',
        'jumlah_unit'      => 'decimal:2',
        'total_bayar'      => 'decimal:2',
        'total_kas_rt'     => 'decimal:2',
        'total_biaya_lain' => 'decimal:2',
        'tarif'            => 'decimal:2',
        'deposit'          => 'decimal:2',
    ];

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function tarifFasilitas(): BelongsTo
    {
        return $this->belongsTo(TarifFasilitas::class, 'tarif_fasilitas_id');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
