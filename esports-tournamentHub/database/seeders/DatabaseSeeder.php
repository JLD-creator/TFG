<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usuariosBase = [
            ['name' => 'Admin Principal', 'email' => 'admin@eshub.test', 'rol' => 'admin'],
            ['name' => 'Organizador Valorant', 'email' => 'organizador.valorant@eshub.test', 'rol' => 'organizador'],
            ['name' => 'Organizador LoL', 'email' => 'organizador.lol@eshub.test', 'rol' => 'organizador'],
            ['name' => 'Test User', 'email' => 'test@example.com', 'rol' => 'jugador'],
        ];

        foreach ($usuariosBase as $usuarioBase) {
            User::updateOrCreate(
                ['email' => $usuarioBase['email']],
                [
                    'name' => $usuarioBase['name'],
                    'password' => 'password',
                    'rol' => $usuarioBase['rol'],
                ]
            );
        }

        $configuracionJuegos = [
            'Valorant' => [
                'equipos' => [
                    'Aether Ravens',
                    'Crimson Vortex',
                    'Pixel Raiders',
                    'Nova Five',
                    'Phantom Unit',
                    'Solar Reapers',
                    'Neon Spectres',
                    'Quantum Rush',
                    'Titan Pulse',
                    'Echo Protocol',
                    'Velocity Prime',
                    'Nightfall Core',
                ],
                'torneos' => [
                    ['nombre' => 'Valorant Campus Open', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_ABIERTO, 'fecha_inicio' => '2026-06-10', 'equipos' => 8],
                    ['nombre' => 'Valorant Rising League', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_ABIERTO, 'fecha_inicio' => '2026-06-18', 'equipos' => 6],
                    ['nombre' => 'Valorant Spring Clash', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_EN_CURSO, 'fecha_inicio' => '2026-05-20', 'equipos' => 8],
                    ['nombre' => 'Valorant Iberia League', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_EN_CURSO, 'fecha_inicio' => '2026-05-12', 'equipos' => 6],
                    ['nombre' => 'Valorant Masters Demo', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_FINALIZADO, 'fecha_inicio' => '2026-04-10', 'equipos' => 8],
                    ['nombre' => 'Valorant Round Robin Finals', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_FINALIZADO, 'fecha_inicio' => '2026-04-03', 'equipos' => 6],
                ],
            ],
            'League of Legends' => [
                'equipos' => [
                    'Baron Breakers',
                    'Nexus Hunters',
                    'Drake Sentinels',
                    'Void Heralds',
                    'Infernal Wolves',
                    'Silver Bolts',
                    'Ancient Guardians',
                    'Blue Buff Crew',
                    'Top Gap Titans',
                    'Jungle Sync',
                    'Elder Storm',
                    'Golden Aegis',
                ],
                'torneos' => [
                    ['nombre' => 'LoL University Cup', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_ABIERTO, 'fecha_inicio' => '2026-06-14', 'equipos' => 8],
                    ['nombre' => 'LoL Rift League', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_ABIERTO, 'fecha_inicio' => '2026-06-22', 'equipos' => 6],
                    ['nombre' => 'LoL Mid Season Playoffs', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_EN_CURSO, 'fecha_inicio' => '2026-05-21', 'equipos' => 8],
                    ['nombre' => 'LoL Rift Series', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_EN_CURSO, 'fecha_inicio' => '2026-05-09', 'equipos' => 6],
                    ['nombre' => 'LoL Champions Bracket', 'tipo_torneo' => 'eliminacion', 'estado' => Torneo::ESTADO_FINALIZADO, 'fecha_inicio' => '2026-04-08', 'equipos' => 8],
                    ['nombre' => 'LoL Elite Round Robin', 'tipo_torneo' => 'liga', 'estado' => Torneo::ESTADO_FINALIZADO, 'fecha_inicio' => '2026-03-30', 'equipos' => 6],
                ],
            ],
        ];

        foreach ($configuracionJuegos as $juego => $configuracion) {
            $equipos = $this->crearEquiposParaJuego($juego, $configuracion['equipos']);
            $this->crearTorneosParaJuego($juego, $configuracion['torneos'], $equipos);
        }
    }

    private function crearEquiposParaJuego(string $juego, array $nombresEquipos): Collection
    {
        $equipos = collect();

        foreach ($nombresEquipos as $nombreEquipo) {
            $slug = Str::slug($nombreEquipo);
            $capitan = User::updateOrCreate(
                ['email' => "{$slug}.capitan@eshub.test"],
                [
                    'name' => "{$nombreEquipo} Captain",
                    'password' => 'password',
                    'rol' => 'jugador',
                ]
            );

            $equipo = Equipo::updateOrCreate(
                ['nombre_equipo' => $nombreEquipo],
                ['id_capitan' => $capitan->id]
            );

            $miembros = collect([$capitan->id]);

            for ($indice = 1; $indice <= 4; $indice++) {
                $jugador = User::updateOrCreate(
                    ['email' => "{$slug}.jugador{$indice}@eshub.test"],
                    [
                        'name' => "{$nombreEquipo} Player {$indice}",
                        'password' => 'password',
                        'rol' => 'jugador',
                    ]
                );

                $miembros->push($jugador->id);
            }

            $equipo->usuarios()->sync($miembros->all());
            $equipos->push($equipo->fresh('usuarios'));
        }

        return $equipos;
    }

    private function crearTorneosParaJuego(string $juego, array $torneos, Collection $equipos): void
    {
        $offset = 0;

        foreach ($torneos as $configuracionTorneo) {
            $torneo = Torneo::updateOrCreate(
                ['nombre' => $configuracionTorneo['nombre']],
                [
                    'juego' => $juego,
                    'tipo_torneo' => $configuracionTorneo['tipo_torneo'],
                    'fecha_inicio' => $configuracionTorneo['fecha_inicio'],
                    'normas' => $this->generarNormas($juego, $configuracionTorneo['tipo_torneo']),
                    'estado' => $configuracionTorneo['estado'],
                ]
            );

            Partido::where('id_torneo', $torneo->id_torneo)->delete();
            Inscripcion::where('id_torneo', $torneo->id_torneo)->delete();

            $equiposTorneo = $equipos
                ->slice($offset, $configuracionTorneo['equipos'])
                ->values();

            if ($equiposTorneo->count() < $configuracionTorneo['equipos']) {
                $equiposTorneo = $equipos
                    ->concat($equipos)
                    ->slice($offset, $configuracionTorneo['equipos'])
                    ->values();
            }

            foreach ($equiposTorneo as $equipo) {
                Inscripcion::create([
                    'id_torneo' => $torneo->id_torneo,
                    'id_equipo' => $equipo->id_equipo,
                ]);
            }

            if ($torneo->tipo_torneo === 'liga') {
                $this->sembrarLiga($torneo, $equiposTorneo, $torneo->estado === Torneo::ESTADO_FINALIZADO);

                if ($torneo->estado === Torneo::ESTADO_EN_CURSO) {
                    $this->marcarPrimerosPartidosComoCompletados($torneo, 4);
                }
            } else {
                $this->sembrarEliminacion($torneo, $equiposTorneo, $torneo->estado);
            }

            $offset = ($offset + 3) % $equipos->count();
        }
    }

    private function sembrarLiga(Torneo $torneo, Collection $equipos, bool $completa): void
    {
        $idsEquipos = $equipos->pluck('id_equipo')->values();
        $ronda = 1;

        for ($i = 0; $i < $idsEquipos->count(); $i++) {
            for ($j = $i + 1; $j < $idsEquipos->count(); $j++) {
                $partido = Partido::create([
                    'id_torneo' => $torneo->id_torneo,
                    'id_equipo1' => $idsEquipos[$i],
                    'id_equipo2' => $idsEquipos[$j],
                    'ronda' => $ronda,
                ]);

                if ($completa) {
                    $this->asignarResultado($partido, $i + 2, $j + 1);
                }

                $ronda = $ronda === 5 ? 1 : $ronda + 1;
            }
        }
    }

    private function sembrarEliminacion(Torneo $torneo, Collection $equipos, string $estado): void
    {
        $idsEquipos = $equipos->pluck('id_equipo')->values();
        $ganadoresRondaUno = collect();

        for ($i = 0; $i < $idsEquipos->count(); $i += 2) {
            $equipo1 = $idsEquipos[$i];
            $equipo2 = $idsEquipos[$i + 1] ?? $idsEquipos[$i];

            $partido = Partido::create([
                'id_torneo' => $torneo->id_torneo,
                'id_equipo1' => $equipo1,
                'id_equipo2' => $equipo2,
                'ronda' => 1,
            ]);

            if ($estado !== Torneo::ESTADO_ABIERTO) {
                $ganadoresRondaUno->push($this->asignarResultado($partido, 2 + $i, 1 + $i));
            }
        }

        if ($estado === Torneo::ESTADO_ABIERTO) {
            return;
        }

        $ganadoresSemis = collect();

        for ($i = 0; $i < $ganadoresRondaUno->count(); $i += 2) {
            if (! isset($ganadoresRondaUno[$i + 1])) {
                $ganadoresSemis->push($ganadoresRondaUno[$i]);
                continue;
            }

            $partido = Partido::create([
                'id_torneo' => $torneo->id_torneo,
                'id_equipo1' => $ganadoresRondaUno[$i],
                'id_equipo2' => $ganadoresRondaUno[$i + 1],
                'ronda' => 2,
            ]);

            if ($estado === Torneo::ESTADO_FINALIZADO) {
                $ganadoresSemis->push($this->asignarResultado($partido, 3 + $i, 2 + $i));
            }
        }

        if ($estado !== Torneo::ESTADO_FINALIZADO || $ganadoresSemis->count() < 2) {
            return;
        }

        $final = Partido::create([
            'id_torneo' => $torneo->id_torneo,
            'id_equipo1' => $ganadoresSemis[0],
            'id_equipo2' => $ganadoresSemis[1],
            'ronda' => 3,
        ]);

        $this->asignarResultado($final, 3, 1);
    }

    private function marcarPrimerosPartidosComoCompletados(Torneo $torneo, int $cantidad): void
    {
        Partido::where('id_torneo', $torneo->id_torneo)
            ->orderBy('ronda')
            ->orderBy('id_partido')
            ->limit($cantidad)
            ->get()
            ->each(function (Partido $partido, int $indice): void {
                $this->asignarResultado($partido, 2 + $indice, 1 + ($indice % 2));
            });
    }

    private function asignarResultado(Partido $partido, int $resultado1, int $resultado2): int
    {
        $ganador = $resultado1 >= $resultado2
            ? $partido->id_equipo1
            : $partido->id_equipo2;

        if ($resultado1 === $resultado2 && $partido->torneo?->tipo_torneo === 'liga') {
            $ganador = null;
        }

        $partido->update([
            'resultado_equipo1' => $resultado1,
            'resultado_equipo2' => $resultado2,
            'ganador' => $ganador,
        ]);

        return $ganador ?? $partido->id_equipo1;
    }

    private function generarNormas(string $juego, string $tipoTorneo): string
    {
        $formato = $tipoTorneo === 'liga'
            ? 'liga regular con todos contra todos y puntuacion por victorias'
            : 'eliminacion directa con series al mejor de 3';

        return "Competicion de {$juego} en formato {$formato}. Check-in 30 minutos antes, respeto deportivo obligatorio y desempatan por mapa/ronda cuando aplique.";
    }
}
