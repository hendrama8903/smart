<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer generik — daftarkan ke semua model yang perlu di-audit.
 */
class AuditObserver
{
    public function created(Model $model): void
    {
        AuditLog::logModel('created', $model);
    }

    public function updated(Model $model): void
    {
        // Hanya log jika ada perubahan yang nyata
        $dirty = $model->getDirty();
        if (empty($dirty)) return;

        // Ambil nilai sebelum perubahan
        $sebelum = array_intersect_key($model->getOriginal(), $dirty);

        AuditLog::logModel('updated', $model, $sebelum);
    }

    public function deleted(Model $model): void
    {
        AuditLog::logModel('deleted', $model);
    }

    // Soft delete: jika model pakai SoftDeletes, ini dipanggil saat soft delete
    public function forceDeleted(Model $model): void
    {
        AuditLog::logModel('force_deleted', $model);
    }
}
