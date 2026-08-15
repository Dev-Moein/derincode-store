<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
    public function hasRole(string $role): bool
{
    return $this->roles()
        ->where('slug', $role)
        ->exists();
}
public function hasPermission(string $permission): bool
{
    return $this->roles()
        ->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })
        ->exists();
}
}
