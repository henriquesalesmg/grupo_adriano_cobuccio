<?php

namespace App\Services;

use App\Models\TransactionCategory;

class TransactionCategoryService
{
    /**
     * Busca ou cria uma categoria de transação pelo nome.
     * @param string $nome
     * @return TransactionCategory
     */
    public static function buscarOuCriar(string $nome): TransactionCategory
    {
        return TransactionCategory::firstOrCreate(['name' => $nome]);
    }
}
