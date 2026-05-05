<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMatchHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_team_match_history(): void
    {
        $user = User::factory()->create(['rol' => 'jugador']);
        $capitan1 = User::factory()->create(['rol' => 'jugador', 'email' => 'hist-cap1@example.com']);
        $capitan2 = User::factory()->create(['rol' => 'jugador', 'email' => 'hist-cap2@example.com']);

        $equipo = Equipo::create([
            'nombre_equipo' => 'Nexus Wolves',
            'id_capitan' => $capitan1->id,
        ]);

        $rival = Equipo::create([
            'nombre_equipo' => 'Shadow Ravens',
            'id_capitan' => $capitan2->id,
        ]);

        $equipo->usuarios()->attach($capitan1->id);
        $rival->usuarios()->attach($capitan2->id);

        $torneo = Torneo::create([
            'nombre' => 'Spring Invitational',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-05-30',
            'normas' => 'BO3',
            'estado' => 'finalizado',
        ]);

        Partido::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo1' => $equipo->id_equipo,
            'id_equipo2' => $rival->id_equipo,
            'ronda' => 1,
            'resultado_equipo1' => 2,
            'resultado_equipo2' => 1,
            'ganador' => $equipo->id_equipo,
        ]);

        Inscripcion::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo' => $equipo->id_equipo,
        ]);

        $response = $this->actingAs($user)->get("/equipos/{$equipo->id_equipo}/historial");

        $response->assertOk();
        $response->assertSee('Nexus Wolves');
        $response->assertSee('Shadow Ravens');
        $response->assertSee('Spring Invitational');
        $response->assertSee('Victoria');
        $response->assertSee('Torneos disputados');
    }
}
