<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ObjectiveHistory extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'objective_uuid',
        'changed_by_uuid',
        'field',
        'old_value',
        'new_value',
        'phase',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function objective()
    {
        return $this->belongsTo(Objective::class, 'objective_uuid', 'uuid');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_uuid', 'uuid');
    }
}
