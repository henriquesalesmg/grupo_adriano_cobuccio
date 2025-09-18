<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthSuccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_welcome_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Carteira Financeira');
    }

    /** @test */
    public function test_user_can_register_successfully()
    {
        $response = $this->post('/register', [
            'name' => 'Teste User',
            'email' => 'teste@example.com',
            'cpf' => '123.456.789-09',
            'birthdate' => '2000-01-01',
            'security_answer' => 'Cruzeiro',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'teste@example.com',
        ]);
    }

    /** @test */
    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'cpf' => '987.654.321-00',
            'birthdate' => '1990-01-01',
            'security_answer' => 'Cruzeiro',
            'password' => bcrypt('Password123!'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create([
            'cpf' => '111.222.333-44',
            'birthdate' => '1995-01-01',
            'security_answer' => 'Cruzeiro',
        ]);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create([
            'cpf' => '555.666.777-88',
            'birthdate' => '1992-01-01',
            'security_answer' => 'Cruzeiro',
            'password' => bcrypt('Password123!'),
        ]);
        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function guest_is_redirected_from_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_access_login_and_register_views()
    {
        $responseLogin = $this->get('/login');
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('Entrar');

        $responseRegister = $this->get('/register');
        $responseRegister->assertStatus(200);
        $responseRegister->assertSee('Criar novo cadastro');
    }
}
