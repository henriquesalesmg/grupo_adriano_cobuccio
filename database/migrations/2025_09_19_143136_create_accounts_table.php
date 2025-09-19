<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('numero', 12)->unique(); // número da conta
            $table->string('agencia', 4)->default('0001');
            $table->enum('tipo', ['corrente', 'poupanca'])->default('corrente');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->enum('status', ['ativa', 'inativa', 'bloqueada'])->default('ativa');
            $table->date('data_abertura');
            $table->string('titular');
            $table->string('documento', 20);
            $table->string('banco', 50)->default('Banco Exemplo');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
