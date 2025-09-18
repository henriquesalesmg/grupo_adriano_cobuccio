<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_form_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Criar novo cadastro');
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'cpf' => '123.456.789-09',
            'birthdate' => '2000-01-01',
            'security_answer' => 'Cruzeiro',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function test_login_form_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Entrar');
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt('Password123!'),
            'cpf' => '987.654.321-00',
            'birthdate' => '1990-01-01',
            'security_answer' => 'Cruzeiro',
        ]);
        $response = $this->post('/login', [
            'email' => 'loginuser@example.com',
            'password' => 'Password123!',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
