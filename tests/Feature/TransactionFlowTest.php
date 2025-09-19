<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;

class TransactionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_credit_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/transaction', [
            'description' => 'Receita Teste',
            'amount' => '100,00',
            'executed_at' => now()->toDateString(),
            'type' => 'receita',
        ]);
        $response->assertRedirect('/transactions');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'description' => 'Receita Teste',
            'amount' => 100.00,
            'type' => 'credit',
        ]);
    }

    public function test_user_can_create_debit_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        // Garante que o usuário tem uma conta
        \App\Models\Account::create([
            'user_id' => $user->id,
            'numero' => '9999',
            'agencia' => '0001',
            'tipo' => 'corrente',
            'saldo' => 0,
            'status' => 'ativa',
            'data_abertura' => now()->toDateString(),
            'titular' => $user->name,
            'documento' => $user->cpf,
            'banco' => 'Banco Exemplo',
        ]);
        // Primeiro, cria um crédito para ter saldo
        Transaction::create([
            'user_id' => $user->id,
            'description' => 'Receita',
            'amount' => 200.00,
            'executed_at' => now()->toDateString(),
            'type' => 'credit',
        ]);
        \App\Services\AccountService::atualizarSaldoPorUsuario($user->id);
        $response = $this->post('/transaction', [
            'description' => 'Despesa Teste',
            'amount' => '50,00',
            'executed_at' => now()->toDateString(),
            'type' => 'debit',
        ]);
        $this->assertAuthenticated();
        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect('http://localhost:8080/transactions');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'description' => 'Despesa Teste',
            'amount' => 50.00,
            'type' => 'debit',
        ]);
    }

    public function test_user_cannot_withdraw_more_than_balance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        // Saldo zero
        $response = $this->post('/transaction', [
            'description' => 'Despesa Teste',
            'amount' => '100,00',
            'executed_at' => now()->toDateString(),
            'type' => 'despesa',
        ]);
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $user->id,
            'description' => 'Despesa Teste',
        ]);
    }

    public function test_user_can_edit_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'description' => 'Receita',
            'amount' => 100.00,
            'executed_at' => now()->toDateString(),
            'type' => 'credit',
        ]);
        $response = $this->put("/transaction/{$transaction->id}", [
            'description' => 'Receita Editada',
            'amount' => '150,00',
            'executed_at' => now()->toDateString(),
            'category_transaction_id' => null,
        ]);
        $response->assertRedirect('/transactions');
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Receita Editada',
            'amount' => 150.00,
        ]);
    }
}
