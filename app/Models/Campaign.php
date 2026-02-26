<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'year',
        'objective_starts_at',
        'objective_stops_at',
        'midterm_starts_at',
        'midterm_stops_at',
        'evaluation_starts_at',
        'evaluation_stops_at',
        'status',
    ];

    protected $casts = [
        'objective_starts_at' => 'date',
        'objective_stops_at' => 'date',
        'midterm_starts_at' => 'date',
        'midterm_stops_at' => 'date',
        'evaluation_starts_at' => 'date',
        'evaluation_stops_at' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'objective_in_progress' => 'Objectifs en cours',
            'objective_completed' => 'Objectifs terminés',
            'midterm_in_progress' => 'Mi-parcours en cours',
            'midterm_completed' => 'Mi-parcours terminée',
            'evaluation_in_progress' => 'Évaluation en cours',
            'evaluation_completed' => 'Évaluation terminée',
            'archived' => 'Archivée',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'objective_in_progress' => 'primary',
            'objective_completed' => 'info',
            'midterm_in_progress' => 'orange',
            'midterm_completed' => 'info',
            'evaluation_in_progress' => 'warning',
            'evaluation_completed' => 'success',
            'archived' => 'dark',
            default => 'secondary',
        };
    }

    public function getNextActionAttribute(): ?array
    {
        return match ($this->status) {
            'draft' => ['action' => 'start-objectives', 'label' => 'Démarrer les objectifs', 'icon' => 'fi-rr-play', 'color' => 'primary'],
            'objective_in_progress' => ['action' => 'complete-objectives', 'label' => 'Terminer les objectifs', 'icon' => 'fi-rr-check', 'color' => 'info'],
            'objective_completed' => ['action' => 'start-midterm', 'label' => 'Démarrer l\'éval. mi-parcours', 'icon' => 'fi-rr-play', 'color' => 'warning'],
            'midterm_in_progress' => ['action' => 'complete-midterm', 'label' => 'Terminer l\'éval. mi-parcours', 'icon' => 'fi-rr-check', 'color' => 'info'],
            'midterm_completed' => ['action' => 'start-evaluations', 'label' => 'Démarrer les évaluations finales', 'icon' => 'fi-rr-play', 'color' => 'warning'],
            'evaluation_in_progress' => ['action' => 'complete-evaluations', 'label' => 'Terminer les évaluations', 'icon' => 'fi-rr-check', 'color' => 'success'],
            'evaluation_completed' => ['action' => 'archive', 'label' => 'Archiver', 'icon' => 'fi-rr-archive', 'color' => 'dark'],
            default => null,
        };
    }

    public function userCampaigns()
    {
        return $this->hasMany(UserCampaign::class, 'campaign_uuid', 'uuid');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_campaigns', 'campaign_uuid', 'user_uuid', 'uuid', 'uuid');
    }
}
