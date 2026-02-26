<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Objective extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_campaign_uuid',
        'objective_category_uuid',
        'title',
        'description',
        'weight',
        'status',
        'rejection_reason',
        'score',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'validated' => 'Validé',
            'rejected' => 'Refusé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'secondary',
            'validated' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function userCampaign()
    {
        return $this->belongsTo(UserCampaign::class, 'user_campaign_uuid', 'uuid');
    }

    public function category()
    {
        return $this->belongsTo(ObjectiveCategory::class, 'objective_category_uuid', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(ObjectiveComment::class, 'objective_uuid', 'uuid')->orderBy('created_at', 'asc');
    }

    public function evaluationComments()
    {
        return $this->hasMany(EvaluationComment::class, 'objective_uuid', 'uuid')->orderBy('created_at', 'asc');
    }

    public function histories()
    {
        return $this->hasMany(ObjectiveHistory::class, 'objective_uuid', 'uuid')->orderBy('created_at', 'desc');
    }
}
