<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'numero' => $this->generateUniqueAccountNumber(),
            'agencia' => '0001',
            'tipo' => $this->faker->randomElement(['corrente', 'poupanca']),
            'saldo' => $this->faker->randomFloat(2, 0, 10000),
            'status' => 'ativa',
            'data_abertura' => now()->toDateString(),
            'titular' => $this->faker->name(),
            'documento' => $this->faker->numerify('###.###.###-##'),
            'banco' => 'Banco Exemplo',
        ];
    }

    private function generateUniqueAccountNumber()
    {
        // Gera um número de conta único de 8 dígitos
        return (string) $this->faker->unique()->numerify('########');
    }
}
