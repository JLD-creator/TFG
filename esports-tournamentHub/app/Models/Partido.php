<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partido extends Model
{
    protected $fillable = [
        'id_torneo',
        'id_equipo1',
        'id_equipo2',
        'ronda',
        'resultado_equipo1',
        'resultado_equipo2',
        'ganador',
    ];

    protected $table = 'partidos';

    protected $primaryKey = 'id_partido';

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'id_torneo', 'id_torneo');
    }

    public function equipo1(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo1', 'id_equipo');
    }

    public function equipo2(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo2', 'id_equipo');
    }

    public function equipoGanador(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'ganador', 'id_equipo');
    }
}
