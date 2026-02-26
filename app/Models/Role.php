<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_uuid', 'uuid');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_uuid', 'uuid');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_uuid', 'permission_uuid')
                     ->withPivot('uuid', 'status')
                     ->withTimestamps();
    }
}
