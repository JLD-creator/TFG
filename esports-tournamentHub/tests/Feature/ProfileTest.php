<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_requires_authentication(): void
    {
        $response = $this->get('/perfil');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'rol' => 'jugador',
        ]);

        $response = $this->actingAs($user)->post('/perfil', [
            'name' => 'Jesus Actualizado',
            'email' => 'jesus.actualizado@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $user->refresh();

        $this->assertSame('Jesus Actualizado', $user->name);
        $this->assertSame('jesus.actualizado@example.com', $user->email);
    }

    public function test_user_can_change_password_by_providing_current_password(): void
    {
        $user = User::factory()->create([
            'rol' => 'jugador',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post('/perfil', [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password123',
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertTrue(Hash::check('nuevaPassword123', $user->password));
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'rol' => 'jugador',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->from('/perfil')->post('/perfil', [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'incorrecta',
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertRedirect('/perfil');
        $response->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
