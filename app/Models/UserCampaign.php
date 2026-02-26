<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserCampaign extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_uuid',
        'campaign_uuid',
        'supervisor_uuid',
        'objective_status',
        'evaluation_status',
        'midterm_file',
        'rating',
        'supervisor_comment',
        'employee_comment',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_uuid', 'uuid');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_uuid', 'uuid');
    }

    public function objectives()
    {
        return $this->hasMany(Objective::class, 'user_campaign_uuid', 'uuid');
    }

    public function decisions()
    {
        return $this->hasMany(ObjectiveDecision::class, 'user_campaign_uuid', 'uuid')->orderBy('created_at', 'desc');
    }

    public function evaluationDecisions()
    {
        return $this->hasMany(EvaluationDecision::class, 'user_campaign_uuid', 'uuid')->orderBy('created_at', 'desc');
    }

    public function getObjectiveStatusLabelAttribute(): string
    {
        return match ($this->objective_status) {
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'returned' => 'Retourné',
            'completed' => 'Terminé',
            default => $this->objective_status,
        };
    }

    public function getObjectiveStatusColorAttribute(): string
    {
        return match ($this->objective_status) {
            'draft' => 'secondary',
            'submitted' => 'warning',
            'returned' => 'info',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    public function getEvaluationStatusLabelAttribute(): string
    {
        return match ($this->evaluation_status) {
            'pending' => 'En attente',
            'supervisor_draft' => 'En cours (Supérieur)',
            'submitted_to_employee' => 'Soumis à l\'employé',
            'returned_to_supervisor' => 'Retourné au supérieur',
            'validated' => 'Validé',
            default => $this->evaluation_status,
        };
    }

    public function getEvaluationStatusColorAttribute(): string
    {
        return match ($this->evaluation_status) {
            'pending' => 'secondary',
            'supervisor_draft' => 'primary',
            'submitted_to_employee' => 'warning',
            'returned_to_supervisor' => 'info',
            'validated' => 'success',
            default => 'secondary',
        };
    }

    public function getRatingLevelAttribute(): ?string
    {
        if ($this->rating === null) return null;
        return match (true) {
            $this->rating < 20 => 'Insuffisant',
            $this->rating < 40 => 'Passable',
            $this->rating < 60 => 'Satisfaisant',
            $this->rating < 80 => 'Bien',
            default => 'Excellent',
        };
    }

    public function getRatingColorAttribute(): string
    {
        if ($this->rating === null) return 'secondary';
        return match (true) {
            $this->rating < 20 => 'danger',
            $this->rating < 40 => 'warning',
            $this->rating < 60 => 'info',
            $this->rating < 80 => 'primary',
            default => 'success',
        };
    }
}
