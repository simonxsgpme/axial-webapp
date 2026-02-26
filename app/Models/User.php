<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'password',
        'phone',
        'position',
        'avatar',
        'is_active',
        'last_login_at',
        'password_changed_at',
        'role_uuid',
        'entity_uuid',
        'supervisor_uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_uuid', 'uuid');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_uuid', 'uuid');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_uuid', 'uuid');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_uuid', 'uuid');
    }

    public function userCampaigns()
    {
        return $this->hasMany(UserCampaign::class, 'user_uuid', 'uuid');
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'user_campaigns', 'user_uuid', 'campaign_uuid', 'uuid', 'uuid');
    }
}
