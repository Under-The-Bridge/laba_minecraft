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

    public function test_a_guest_can_register_and_open_their_inventory(): void
    {
        $password = Str::random(20);
        $this->get('/login')->assertOk()->assertSee('Зарегистрироваться');
        $this->get('/register')->assertOk()->assertSee('Повтори пароль');

        $this->post('/register', [
            'name' => 'Новый игрок',
            'email' => 'new-player@example.test',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect('/');

        $player = User::where('email', 'new-player@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($player);
        $this->assertTrue(Hash::check($password, $player->password));
        $this->get('/inventory')->assertOk()->assertSee($player->name)->assertSee('Инвентарь пока пуст');
    }

    public function test_registration_rejects_a_taken_email_and_password_mismatch(): void
    {
        $player = User::factory()->create();

        $this->from('/register')->post('/register', [
            'name' => 'Другой игрок',
            'email' => $player->email,
            'password' => Str::random(20),
            'password_confirmation' => Str::random(21),
        ])->assertRedirect('/register')->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }
}
