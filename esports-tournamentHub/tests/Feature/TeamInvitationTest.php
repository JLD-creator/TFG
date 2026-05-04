<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\InvitacionEquipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_captain_can_invite_registered_player(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $invitado = User::factory()->create(['rol' => 'jugador', 'email' => 'invitado@example.com']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);
        $equipo->usuarios()->attach($capitan->id);

        $response = $this->actingAs($capitan)->post("/equipos/{$equipo->id_equipo}/invitar", [
            'email' => 'invitado@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invitaciones_equipo', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario_invitado' => $invitado->id,
            'estado' => 'pendiente',
        ]);
    }

    public function test_invited_player_can_accept_team_invitation(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $invitado = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);
        $equipo->usuarios()->attach($capitan->id);

        $invitacion = InvitacionEquipo::create([
            'id_equipo' => $equipo->id_equipo,
            'id_usuario_invitado' => $invitado->id,
            'id_usuario_invitador' => $capitan->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($invitado)->post("/invitaciones-equipo/{$invitacion->id_invitacion}/aceptar");

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('equipo_usuario', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $invitado->id,
        ]);

        $this->assertDatabaseHas('invitaciones_equipo', [
            'id_invitacion' => $invitacion->id_invitacion,
            'estado' => 'aceptada',
        ]);
    }

    public function test_non_captain_cannot_send_team_invitations(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $otroJugador = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);
        $equipo->usuarios()->attach($capitan->id);

        $response = $this->actingAs($otroJugador)->post("/equipos/{$equipo->id_equipo}/invitar", [
            'email' => 'nadie@example.com',
        ]);

        $response->assertForbidden();
    }
}
