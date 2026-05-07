<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueTournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_generating_league_creates_round_robin_matches(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);
        $torneo = Torneo::create([
            'nombre' => 'Liga Valorant',
            'juego' => 'Valorant',
            'tipo_torneo' => 'liga',
            'fecha_inicio' => '2026-05-25',
            'normas' => 'Todos contra todos.',
            'estado' => 'abierto',
        ]);

        $equipos = collect(range(1, 4))->map(function (int $indice) {
            $capitan = User::factory()->create([
                'rol' => 'jugador',
                'email' => "capitan{$indice}@example.com",
            ]);

            $equipo = Equipo::create([
                'nombre_equipo' => "Equipo {$indice}",
                'id_capitan' => $capitan->id,
            ]);

            $equipo->usuarios()->attach($capitan->id);

            return $equipo;
        });

        foreach ($equipos as $equipo) {
            Inscripcion::create([
                'id_torneo' => $torneo->id_torneo,
                'id_equipo' => $equipo->id_equipo,
            ]);
        }

        $response = $this->actingAs($organizador)->post("/torneos/{$torneo->id_torneo}/bracket");

        $response->assertSessionHasNoErrors();
        $this->assertSame(6, Partido::where('id_torneo', $torneo->id_torneo)->count());
    }

    public function test_league_allows_draw_results(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);
        $torneo = Torneo::create([
            'nombre' => 'Liga Valorant',
            'juego' => 'Valorant',
            'tipo_torneo' => 'liga',
            'fecha_inicio' => '2026-05-25',
            'normas' => 'Todos contra todos.',
            'estado' => 'abierto',
        ]);
        $capitan1 = User::factory()->create(['rol' => 'jugador', 'email' => 'capitan-liga-1@example.com']);
        $capitan2 = User::factory()->create(['rol' => 'jugador', 'email' => 'capitan-liga-2@example.com']);
        $equipo1 = Equipo::create([
            'nombre_equipo' => 'Equipo Liga 1',
            'id_capitan' => $capitan1->id,
        ]);
        $equipo2 = Equipo::create([
            'nombre_equipo' => 'Equipo Liga 2',
            'id_capitan' => $capitan2->id,
        ]);
        $equipo1->usuarios()->attach($capitan1->id);
        $equipo2->usuarios()->attach($capitan2->id);
        $partido = Partido::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo1' => $equipo1->id_equipo,
            'id_equipo2' => $equipo2->id_equipo,
            'ronda' => 1,
        ]);

        $response = $this->actingAs($organizador)->post("/partidos/{$partido->id_partido}/resultado", [
            'resultado_equipo1' => 2,
            'resultado_equipo2' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $partido->refresh();

        $this->assertNull($partido->ganador);
        $this->assertSame(2, $partido->resultado_equipo1);
        $this->assertSame(2, $partido->resultado_equipo2);
    }

    public function test_direct_elimination_with_odd_teams_creates_a_bye_in_first_round(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);
        $torneo = Torneo::create([
            'nombre' => 'Copa Valorant',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-05-25',
            'normas' => 'Eliminacion directa.',
            'estado' => 'abierto',
        ]);

        $equipos = collect(range(1, 5))->map(function (int $indice) {
            $capitan = User::factory()->create([
                'rol' => 'jugador',
                'email' => "capitan-elim-{$indice}@example.com",
            ]);

            $equipo = Equipo::create([
                'nombre_equipo' => "Equipo Eliminacion {$indice}",
                'id_capitan' => $capitan->id,
            ]);

            $equipo->usuarios()->attach($capitan->id);

            return $equipo;
        });

        foreach ($equipos as $equipo) {
            Inscripcion::create([
                'id_torneo' => $torneo->id_torneo,
                'id_equipo' => $equipo->id_equipo,
            ]);
        }

        $response = $this->actingAs($organizador)->post("/torneos/{$torneo->id_torneo}/bracket");

        $response->assertSessionHasNoErrors();

        $partidos = Partido::where('id_torneo', $torneo->id_torneo)->get();

        $this->assertSame(3, $partidos->count());
        $this->assertTrue($partidos->contains(function (Partido $partido) {
            return $partido->id_equipo1 === $partido->id_equipo2
                && $partido->ganador !== null
                && $partido->resultado_equipo1 === 1
                && $partido->resultado_equipo2 === 0;
        }));
    }
}
