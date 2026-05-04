<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $fillable = [
        'nombre_equipo',
        'id_capitan',
    ];

    protected $table = 'equipos';

    protected $primaryKey = 'id_equipo';

    public function capitan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_capitan');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'equipo_usuario',
            'id_equipo',
            'id_usuario'
        );
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'id_equipo', 'id_equipo');
    }

    public function invitaciones(): HasMany
    {
        return $this->hasMany(InvitacionEquipo::class, 'id_equipo', 'id_equipo');
    }
}
