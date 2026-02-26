<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ObjectiveDecision extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_campaign_uuid',
        'actor_uuid',
        'action',
        'comment',
    ];

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

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_uuid', 'uuid');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'submitted' => 'Objectifs soumis',
            'returned' => 'Objectifs retournés',
            'completed' => 'Objectifs validés',
            default => $this->action,
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'submitted' => 'primary',
            'returned' => 'warning',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'submitted' => 'fi fi-rr-paper-plane',
            'returned' => 'fi fi-rr-undo',
            'completed' => 'fi fi-rr-check-circle',
            default => 'fi fi-rr-info',
        };
    }
}
