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
             // 🔹 Cria a nova estrutura da tabela
             Schema::create('pix_account', function (Blueprint $table) {
                $table->id();
                $table->string('bank_name'); // Nome do banco (ex: Banco do Brasil, Itaú)
                $table->string('pix_key'); // Chave Pix cadastrada
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
