<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Role;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'parent_id', 'nama', 'type', 'icon', 'controller', 'fungsi', 'url', 'urutan', 'roles', 'aktif',
    ];

    protected $casts = [
        'aktif'  => 'boolean',
        'urutan' => 'integer',
    ];

    // ---------- Relasi parent / sub ----------
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('urutan');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(MenuPermission::class);
    }

    // ---------- Helper ----------

    // Bangun URL link menu. Prioritas: url manual -> controller@fungsi -> '#'
    public function link(): string
    {
        if (! empty($this->url)) {
            return url($this->url);
        }

        if (! empty($this->controller) && ! empty($this->fungsi)) {
            $class = 'App\\Http\\Controllers\\' . $this->controller;
            try {
                return action([$class, $this->fungsi]);
            } catch (\Throwable) {
                return '#';
            }
        }

        return '#';
    }

    // Apakah menu ini halaman yang sedang dibuka
    public function isActive(): bool
    {
        $link = $this->link();
        return $link !== '#' && rtrim(url()->current(), '/') === rtrim($link, '/');
    }

    // Apakah role user boleh melihat menu ini (roles kosong = semua boleh)
    public function visibleTo(User $user): bool
    {
        if (blank($this->roles)) {
            return true;
        }
        $izin = array_map('trim', explode(',', $this->roles));
        return in_array(optional($user->role)->nama, $izin, true);
    }

    // Ambil izin aksi untuk role tertentu. Jika tidak ada record = akses penuh.
    public function permsFor(?Role $role): array
    {
        $default = ['add' => true, 'edit' => true, 'delete' => true, 'export' => true];

        if (! $role) {
            return $default;
        }

        $perm = $this->permissions->firstWhere('role_id', $role->id);

        if (! $perm) {
            return $default;
        }

        return [
            'add'    => (bool) $perm->can_add,
            'edit'   => (bool) $perm->can_edit,
            'delete' => (bool) $perm->can_delete,
            'export' => (bool) $perm->can_export,
        ];
    }
}
