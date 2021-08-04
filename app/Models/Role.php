<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = [];
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super';
    }
}
