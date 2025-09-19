<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;

class DemoUsersAndTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Salário', 'Aluguel', 'Transferência', 'Depósito', 'Saque'
        ];
        $categoryIds = [];
        foreach ($categories as $cat) {
            $categoryIds[$cat] = TransactionCategory::firstOrCreate([
                'name' => $cat
            ], [
                'description' => $cat
            ])->id;
        }

        for ($i = 1; $i <= 4; $i++) {
            $user = User::create([
                'name' => "Usuário $i",
                'email' => "usuario$i@email.com",
                'cpf' => sprintf('000.000.000-0%d', $i),
                'birthdate' => now()->subYears(20 + $i)->toDateString(),
                'security_answer' => 'Azul',
                'password' => Hash::make('senha123'),
            ]);

            $account = Account::create([
                'user_id' => $user->id,
                'numero' => '1000' . $i,
                'agencia' => '0001',
                'tipo' => 'corrente',
                'saldo' => 1000 * $i,
                'status' => 'ativa',
                'data_abertura' => now()->subMonths($i),
                'titular' => $user->name,
                'documento' => $user->cpf,
                'banco' => 'Banco Exemplo',
            ]);

            $tipos = ['credit', 'debit', 'transfer', 'credit', 'debit'];
            for ($j = 0; $j < 5; $j++) {
                $tipo = $tipos[$j];
                $cat = $tipo === 'credit' ? 'Depósito' : ($tipo === 'debit' ? 'Saque' : 'Transferência');
                $destino = $tipo === 'transfer' ? '10001' : null;
                $destino_tipo = $tipo === 'transfer' ? 'conta' : null;
                Transaction::create([
                    'user_id' => $user->id,
                    'category_transaction_id' => $categoryIds[$cat],
                    'amount' => rand(100, 1000),
                    'type' => $tipo,
                    'description' => $cat . ' automática',
                    'executed_at' => now()->subDays($j),
                    'destino_tipo' => $destino_tipo,
                    'destino' => $destino,
                    'reverted' => false,
                ]);
            }
        }
    }
}
