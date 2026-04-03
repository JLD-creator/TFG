<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function equipos(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipo::class,
            'equipo_usuario',
            'id_usuario',
            'id_equipo'
        );
    }

    public function esAdmin(): bool
    {
        return $this->rol == 'admin';
    }

    public function esOrganizador(): bool
    {
        return $this->rol == 'organizador';
    }
}
