<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Entity extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'acronym',
        'category',
        'parent_uuid',
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
        return $this->hasMany(User::class, 'entity_uuid', 'uuid');
    }

    public function parent()
    {
        return $this->belongsTo(Entity::class, 'parent_uuid', 'uuid');
    }

    public function children()
    {
        return $this->hasMany(Entity::class, 'parent_uuid', 'uuid');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        return implode(' > ', $path);
    }

    public function getAllDescendantUuids(): array
    {
        $uuids = [$this->uuid];
        foreach ($this->children as $child) {
            $uuids = array_merge($uuids, $child->getAllDescendantUuids());
        }
        return $uuids;
    }
}
