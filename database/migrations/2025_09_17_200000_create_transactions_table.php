<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('category_transaction_id')->nullable();
            $table->foreign('category_transaction_id')->references('id')->on('transaction_categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit', 'transfer']);
            $table->string('description')->nullable();
            $table->date('executed_at')->nullable();
            $table->string('destino_tipo')->nullable();
            $table->string('destino')->nullable();
            $table->boolean('reverted')->default(false)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
