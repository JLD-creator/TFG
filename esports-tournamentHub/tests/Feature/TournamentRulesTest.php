<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_create_tournament_with_rules(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);

        $response = $this->actingAs($organizador)->post('/torneos', [
            'nombre' => 'Spring Invitational',
            'juego' => 'Valorant',
            'tipo_torneo' => 'eliminacion',
            'fecha_inicio' => '2026-05-20',
            'normas' => 'Formato BO3, check-in 30 minutos antes y prohibido el uso de cuentas compartidas.',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('torneos', [
            'nombre' => 'Spring Invitational',
            'normas' => 'Formato BO3, check-in 30 minutos antes y prohibido el uso de cuentas compartidas.',
        ]);
    }

    public function test_tournament_rules_are_required(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);

        $response = $this->actingAs($organizador)
            ->from('/torneos/create')
            ->post('/torneos', [
                'nombre' => 'Spring Invitational',
                'juego' => 'Valorant',
                'tipo_torneo' => 'eliminacion',
                'fecha_inicio' => '2026-05-20',
                'normas' => '',
            ]);

        $response->assertRedirect('/torneos/create');
        $response->assertSessionHasErrors('normas');
    }
}
