<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_cannot_subscribe_when_tournament_is_not_open(): void
    {
        $jugador = User::factory()->create(['rol' => 'jugador']);
        $capitan = User::factory()->create(['rol' => 'jugador', 'email' => 'capitan-estado@example.com']);
        $equipo = Equipo::create([
            'nombre_equipo' => 'Equipo Estado',
            'id_capitan' => $capitan->id,
        ]);
        $equipo->usuarios()->attach($jugador->id);
        $torneo = Torneo::create([
            'nombre' => 'Torneo Cerrado',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-05-10',
            'normas' => 'Normas',
            'estado' => Torneo::ESTADO_EN_CURSO,
        ]);

        $response = $this->actingAs($jugador)->post("/torneos/{$torneo->id_torneo}/inscribirse");

        $response->assertSessionHas('error');
    }

    public function test_cannot_register_result_when_tournament_is_not_in_progress(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);
        $capitan1 = User::factory()->create(['rol' => 'jugador', 'email' => 'estado-cap1@example.com']);
        $capitan2 = User::factory()->create(['rol' => 'jugador', 'email' => 'estado-cap2@example.com']);
        $equipo1 = Equipo::create([
            'nombre_equipo' => 'Equipo Estado 1',
            'id_capitan' => $capitan1->id,
        ]);
        $equipo2 = Equipo::create([
            'nombre_equipo' => 'Equipo Estado 2',
            'id_capitan' => $capitan2->id,
        ]);
        $torneo = Torneo::create([
            'nombre' => 'Torneo Abierto',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-05-10',
            'normas' => 'Normas',
            'estado' => Torneo::ESTADO_ABIERTO,
        ]);
        $partido = Partido::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo1' => $equipo1->id_equipo,
            'id_equipo2' => $equipo2->id_equipo,
            'ronda' => 1,
        ]);

        $response = $this->actingAs($organizador)->post("/partidos/{$partido->id_partido}/resultado", [
            'resultado_equipo1' => 2,
            'resultado_equipo2' => 1,
        ]);

        $response->assertSessionHas('error');
    }
}
