<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk()->assertSee('Рецептов пока нет');
    }

    public function test_a_player_can_log_in_and_log_out(): void
    {
        $password = Str::random(20);
        $player = User::factory()->create(['password' => Hash::make($password)]);

        $this->get('/inventory')->assertRedirect('/login');
        $this->get('/login')->assertOk();
        $this->post('/login', ['email' => $player->email, 'password' => Str::random(20)])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $player->email, 'password' => $password])
            ->assertRedirect('/inventory');
        $this->assertAuthenticatedAs($player);
        $this->get('/inventory')->assertOk()->assertSee($player->name);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
