<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public    $timestamps  = false;
    protected $table       = 'audit_logs';
    protected $guarded     = [];
    protected $casts       = [
        'sebelum'    => 'array',
        'sesudah'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Static helper untuk log manual ──────────────────────────────────
    public static function log(
        string  $aksi,
        ?string $modul       = null,
        ?int    $modulId     = null,
        ?string $deskripsi   = null,
        ?array  $sebelum     = null,
        ?array  $sesudah     = null
    ): void {
        try {
            $user = Auth::user();
            static::create([
                'user_id'    => $user?->id,
                'nama_user'  => $user?->name ?? 'System',
                'aksi'       => $aksi,
                'modul'      => $modul,
                'modul_id'   => $modulId,
                'deskripsi'  => $deskripsi,
                'sebelum'    => $sebelum,
                'sesudah'    => $sesudah,
                'ip_address' => Request::ip(),
                'user_agent' => substr(Request::userAgent() ?? '', 0, 255),
            ]);
        } catch (\Throwable) {
            // Jangan sampai audit gagal merusak proses utama
        }
    }

    // ─── Log dari model observer ──────────────────────────────────────────
    public static function logModel(string $aksi, Model $model, ?array $sebelum = null): void
    {
        $modul     = class_basename($model);
        $label     = static::labelFor($model);
        $deskripsi = match ($aksi) {
            'created' => "{$modul} baru ditambahkan: {$label}",
            'updated' => "{$modul} diperbarui: {$label}",
            'deleted' => "{$modul} dihapus: {$label}",
            default   => "{$aksi} {$modul}: {$label}",
        };

        $sesudah  = $aksi !== 'deleted' ? static::filterFields($model->toArray()) : null;
        $sebelum  = $sebelum ? static::filterFields($sebelum) : null;

        static::log($aksi, $modul, $model->id, $deskripsi, $sebelum, $sesudah);
    }

    // Ambil label ringkas dari model
    private static function labelFor(Model $model): string
    {
        return $model->nama ?? $model->judul ?? $model->nama_pos ?? $model->kepala_keluarga ?? "ID #{$model->id}";
    }

    // Hapus field sensitif dan berlebihan dari log
    private static function filterFields(array $data): array
    {
        $skip = ['password', 'remember_token', 'updated_at', 'created_at', 'deleted_at'];
        return array_filter($data, fn ($k) => ! in_array($k, $skip), ARRAY_FILTER_USE_KEY);
    }

    // ─── Label aksi untuk tampilan ────────────────────────────────────────
    public function getAksiLabelAttribute(): string
    {
        return match($this->aksi) {
            'created'  => 'Tambah',
            'updated'  => 'Ubah',
            'deleted'  => 'Hapus',
            'login'    => 'Login',
            'logout'   => 'Logout',
            'export'   => 'Export',
            'bayar'    => 'Bayar Iuran',
            'generate' => 'Generate Tagihan',
            'upload'   => 'Upload File',
            'import'   => 'Import Data',
            default    => ucfirst($this->aksi),
        };
    }

    public function getAksiColorAttribute(): string
    {
        return match($this->aksi) {
            'created','bayar','generate','import' => 'green',
            'updated','upload'                    => 'blue',
            'deleted'                             => 'red',
            'login'                               => 'teal',
            'logout'                              => 'gray',
            'export'                              => 'purple',
            default                               => 'gray',
        };
    }
}
