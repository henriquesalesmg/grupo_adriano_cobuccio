<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Cria uma nova transação para o usuário.
     * @param array $dados
     * @return Transaction
     */
    public static function novaTransacao(array $dados): Transaction
    {
        return self::create($dados);
    }
    protected $table = 'transactions';
    use HasFactory;


    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'executed_at',
        'category_transaction_id',
        'destino_tipo',
        'destino',
        'reverted',
    ];

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_transaction_id');
    }
}
