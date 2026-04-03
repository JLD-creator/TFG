<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    protected $fillable = [
        'nombre',
        'juego',
        'tipo_torneo',
        'fecha_inicio',
        'estado',
    ];

    protected $table = 'torneos';

    protected $primaryKey = 'id_torneo';

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'id_torneo', 'id_torneo');
    }

    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class, 'id_torneo', 'id_torneo');
    }
}
