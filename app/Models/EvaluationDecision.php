<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EvaluationDecision extends Model
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
            'submitted_to_employee' => 'Évaluation soumise à l\'employé',
            'returned_to_supervisor' => 'Évaluation retournée au supérieur',
            'validated' => 'Évaluation validée',
            default => $this->action,
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'submitted_to_employee' => 'primary',
            'returned_to_supervisor' => 'warning',
            'validated' => 'success',
            default => 'secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'submitted_to_employee' => 'fi fi-rr-paper-plane',
            'returned_to_supervisor' => 'fi fi-rr-undo',
            'validated' => 'fi fi-rr-check-circle',
            default => 'fi fi-rr-info',
        };
    }
}
