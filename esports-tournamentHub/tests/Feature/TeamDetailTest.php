<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_team_detail(): void
    {
        $user = User::factory()->create(['rol' => 'jugador']);
        $capitan = User::factory()->create(['rol' => 'jugador', 'email' => 'detalle-capitan@example.com']);
        $miembro = User::factory()->create(['rol' => 'jugador', 'email' => 'detalle-miembro@example.com']);

        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan->id,
        ]);
        $equipo->usuarios()->attach([$capitan->id, $miembro->id]);

        $torneo = Torneo::create([
            'nombre' => 'Spring Invitational',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-06-01',
            'normas' => 'BO3',
            'estado' => Torneo::ESTADO_ABIERTO,
        ]);

        Inscripcion::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo' => $equipo->id_equipo,
        ]);

        $response = $this->actingAs($user)->get("/equipos/{$equipo->id_equipo}");

        $response->assertOk();
        $response->assertSee('Nexus Wolves');
        $response->assertSee('Spring Invitational');
        $response->assertSee($capitan->name);
        $response->assertSee($miembro->name);
    }
}
