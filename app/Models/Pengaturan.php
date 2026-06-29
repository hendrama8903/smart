<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    public $timestamps = true;

    // Ambil nilai pengaturan: Pengaturan::ambil('tarif_pendopo_warga')
    public static function ambil(string $kunci, $default = null)
    {
        return optional(static::where('kunci', $kunci)->first())->nilai ?? $default;
    }

    // Simpan/ubah nilai: Pengaturan::simpan('rt', '001')
    public static function simpan(string $kunci, $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }
}
