<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPermission extends Model
{
    protected $table = 'menu_permissions';

    protected $fillable = ['menu_id', 'role_id', 'can_add', 'can_edit', 'can_delete', 'can_export'];

    protected $casts = [
        'can_add'    => 'boolean',
        'can_edit'   => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
