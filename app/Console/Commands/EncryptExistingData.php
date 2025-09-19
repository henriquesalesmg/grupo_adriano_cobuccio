<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Traits\EncryptableFields;
use Illuminate\Support\Facades\DB;

class EncryptExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:existing-data
                            {--model=User : O modelo a ser processado}
                            {--field= : Campo específico para criptografar}
                            {--dry-run : Executa sem fazer alterações}
                            {--force : Força a execução sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criptografa dados sensíveis existentes no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->option('model');
        $field = $this->option('field');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("🔐 Iniciando criptografia de dados sensíveis...");

        if ($dryRun) {
            $this->warn("⚠️  Modo DRY RUN - Nenhuma alteração será feita no banco");
        }

        switch ($model) {
            case 'User':
                $this->encryptUserData($field, $dryRun, $force);
                break;
            default:
                $this->error("Modelo '{$model}' não suportado.");
                return 1;
        }

        $this->info("✅ Processo de criptografia concluído!");
        return 0;
    }

    /**
     * Criptografa dados do modelo User
     */
    protected function encryptUserData(?string $specificField, bool $dryRun, bool $force): void
    {
        $users = User::all();
        $encryptableFields = ['cpf', 'security_answer'];

        if ($specificField) {
            if (!in_array($specificField, $encryptableFields)) {
                $this->error("Campo '{$specificField}' não é criptografável.");
                return;
            }
            $encryptableFields = [$specificField];
        }

        $totalUsers = $users->count();
        $this->info("📊 Encontrados {$totalUsers} usuários para processamento");

        if (!$force && !$dryRun) {
            if (!$this->confirm("Deseja continuar com a criptografia dos dados?")) {
                $this->info("Operação cancelada.");
                return;
            }
        }

        $processedCount = 0;
        $encryptedCount = 0;
        $errorCount = 0;

        $this->output->progressStart($totalUsers);

        foreach ($users as $user) {
            try {
                $hasChanges = false;

                foreach ($encryptableFields as $field) {
                    $value = $user->getAttributes()[$field] ?? null;

                    if (empty($value)) {
                        continue;
                    }

                    // Verifica se já está criptografado
                    if ($this->isEncrypted($value)) {
                        continue;
                    }

                    if (!$dryRun) {
                        // Usa método raw para evitar o trait durante a atualização
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                $field => encrypt($value)
                            ]);
                    }

                    $hasChanges = true;
                    $this->line("🔒 Criptografado {$field} para usuário ID {$user->id}");
                }

                if ($hasChanges) {
                    $encryptedCount++;
                }

                $processedCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Erro ao processar usuário ID {$user->id}: " . $e->getMessage());
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info("📈 Estatísticas:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Usuários processados', $processedCount],
                ['Usuários com dados criptografados', $encryptedCount],
                ['Erros encontrados', $errorCount],
                ['Campos processados', implode(', ', $encryptableFields)]
            ]
        );

        if ($dryRun) {
            $this->warn("⚠️  Esta foi uma execução de teste. Para aplicar as alterações, execute sem --dry-run");
        }
    }

    /**
     * Verifica se um valor já está criptografado
     */
    protected function isEncrypted(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        try {
            decrypt($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mostra informações sobre campos criptografáveis
     */
    protected function showEncryptableFields(): void
    {
        $this->info("📋 Campos criptografáveis por modelo:");

        $this->table(
            ['Modelo', 'Campos'],
            [
                ['User', 'cpf, security_answer'],
            ]
        );
    }
}
