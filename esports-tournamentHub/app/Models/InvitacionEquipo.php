<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitacionEquipo extends Model
{
    protected $table = 'invitaciones_equipo';

    protected $primaryKey = 'id_invitacion';

    protected $fillable = [
        'id_equipo',
        'id_usuario_invitado',
        'id_usuario_invitador',
        'estado',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo', 'id_equipo');
    }

    public function invitado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_invitado');
    }

    public function invitador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_invitador');
    }
}
