<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pix_account', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name'); // Nome do banco (Banco do Brasil, Itaú, Bradesco)
            $table->string('pix_key'); // Chave Pix cadastrada
            $table->string('token_url'); // URL para obter o token
            $table->string('pix_url'); // URL da API Pix para cobrança
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pix_account');
    }
};
