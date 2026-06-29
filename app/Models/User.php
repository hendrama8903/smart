<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'role_id', 'warga_id', 'name', 'username', 'email', 'password', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---------- Relasi ----------
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    // ---------- Helper hak akses (RBAC) ----------
    public function hasRole(string ...$nama): bool
    {
        return in_array(optional($this->role)->nama, $nama, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPengurus(): bool
    {
        return $this->hasRole('admin', 'ketua', 'sekretaris', 'bendahara');
    }

    public function isWarga(): bool
    {
        return $this->hasRole('warga');
    }
}
