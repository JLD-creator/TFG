<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    public const ESTADO_ABIERTO = 'abierto';

    public const ESTADO_EN_CURSO = 'en_curso';

    public const ESTADO_FINALIZADO = 'finalizado';

    protected $fillable = [
        'nombre',
        'juego',
        'tipo_torneo',
        'fecha_inicio',
        'normas',
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

    public function estaAbierto(): bool
    {
        return $this->estado === self::ESTADO_ABIERTO;
    }

    public function estaEnCurso(): bool
    {
        return $this->estado === self::ESTADO_EN_CURSO;
    }

    public function estaFinalizado(): bool
    {
        return $this->estado === self::ESTADO_FINALIZADO;
    }
}
