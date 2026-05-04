<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_captain_can_remove_a_member_from_team(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $miembro = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach([$capitan->id, $miembro->id]);

        $response = $this->actingAs($capitan)->post("/equipos/{$equipo->id_equipo}/miembros/{$miembro->id}/expulsar");

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('equipo_usuario', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $miembro->id,
        ]);
    }

    public function test_member_can_leave_team(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $miembro = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach([$capitan->id, $miembro->id]);

        $response = $this->actingAs($miembro)->post("/equipos/{$equipo->id_equipo}/salir");

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('equipo_usuario', [
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $miembro->id,
        ]);
    }

    public function test_captain_leaving_transfers_captaincy_to_another_member(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $miembro = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach([$capitan->id, $miembro->id]);

        $response = $this->actingAs($capitan)->post("/equipos/{$equipo->id_equipo}/salir");

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $equipo->refresh();

        $this->assertSame($miembro->id, $equipo->id_capitan);
    }

    public function test_last_member_leaving_deletes_team(): void
    {
        $capitan = User::factory()->create(['rol' => 'jugador']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);

        $equipo->usuarios()->attach($capitan->id);

        $response = $this->actingAs($capitan)->post("/equipos/{$equipo->id_equipo}/salir");

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('equipos', [
            'id_equipo' => $equipo->id_equipo,
        ]);
    }
}
