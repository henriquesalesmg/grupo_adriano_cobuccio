<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'numero',
        'agencia',
        'tipo',
        'saldo',
        'status',
        'data_abertura',
        'titular',
        'documento',
        'banco',
    ];

    /**
     * Relacionamento: usuário dono da conta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Recalcula e atualiza o saldo da conta do usuário com base nas transações até hoje.
     *
     * @param int $userId
     * @return void
     */
    // Função de atualização de saldo movida para App\Services\AccountService
}
