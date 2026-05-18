<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_jugador_cannot_access_tournament_creation(): void
    {
        $jugador = User::factory()->create(['rol' => 'jugador']);

        $response = $this->actingAs($jugador)->get('/torneos/create');

        $response->assertForbidden();
    }

    public function test_organizador_cannot_create_or_join_teams(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);

        $createResponse = $this->actingAs($organizador)->get('/equipos/create');
        $joinResponse = $this->actingAs($organizador)->post('/equipos/1/unirse');

        $createResponse->assertForbidden();
        $joinResponse->assertForbidden();
    }

    public function test_admin_cannot_subscribe_team_to_tournament(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);

        $response = $this->actingAs($admin)->post('/torneos/1/inscribirse');

        $response->assertForbidden();
    }

    public function test_organizador_can_access_tournament_creation(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);

        $response = $this->actingAs($organizador)->get('/torneos/create');

        $response->assertOk();
    }

    public function test_only_admin_can_access_user_role_management(): void
    {
        $organizador = User::factory()->create(['rol' => 'organizador']);

        $response = $this->actingAs($organizador)->get('/admin/usuarios');

        $response->assertForbidden();
    }

    public function test_admin_can_update_user_roles(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $jugador = User::factory()->create(['rol' => 'jugador']);

        $response = $this->actingAs($admin)->post("/admin/usuarios/{$jugador->id}/rol", [
            'rol' => 'organizador',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $jugador->refresh();

        $this->assertSame('organizador', $jugador->rol);
    }

    public function test_role_changes_done_in_database_are_reflected_without_logging_in_again(): void
    {
        $usuario = User::factory()->create(['rol' => 'jugador']);

        $this->actingAs($usuario)
            ->get('/admin/usuarios')
            ->assertForbidden();

        User::whereKey($usuario->id)->update([
            'rol' => 'admin',
        ]);

        $this->get('/admin/usuarios')->assertOk();
    }
}
