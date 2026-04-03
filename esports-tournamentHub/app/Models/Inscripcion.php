<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Inscripcion extends Model
{
    protected $fillable = [
        'id_torneo',
        'id_equipo',
    ];

    protected $table = 'inscripciones';

    protected $primaryKey = 'id_inscripcion';

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'id_torneo', 'id_torneo');
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo', 'id_equipo');
    }
}
