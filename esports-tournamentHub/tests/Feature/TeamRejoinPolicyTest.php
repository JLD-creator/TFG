<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\EquipoBaja;
use App\Models\InvitacionEquipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamRejoinPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_who_left_team_cannot_rejoin_directly(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador', 'email' => 'capitan-rejoin@example.com']);
        $jugador = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Rejoin Squad',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach([$capitan->id, $jugador->id]);

        $this->actingAs($jugador)->post("/equipos/{$equipo->id_equipo}/salir");

        $response = $this->actingAs($jugador)->post("/equipos/{$equipo->id_equipo}/unirse");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('equipo_bajas', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $jugador->id,
        ]);
        $this->assertDatabaseMissing('equipo_usuario', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $jugador->id,
        ]);
    }

    public function test_user_can_rejoin_if_new_invitation_is_accepted(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador', 'email' => 'capitan-invita@example.com']);
        $jugador = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Invite Back Squad',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach($capitan->id);
        EquipoBaja::create([
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $jugador->id,
        ]);

        $invitacion = InvitacionEquipo::create([
            'id_equipo' => $equipo->id_equipo,
            'id_usuario_invitado' => $jugador->id,
            'id_usuario_invitador' => $capitan->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($jugador)->post("/invitaciones-equipo/{$invitacion->id_invitacion}/aceptar");

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('equipo_usuario', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $jugador->id,
        ]);
        $this->assertDatabaseMissing('equipo_bajas', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $jugador->id,
        ]);
    }
}
