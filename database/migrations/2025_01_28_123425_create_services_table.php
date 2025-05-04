<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do serviço
            $table->integer('duration'); // Tempo em minutos
            $table->decimal('price', 8, 2); // Preço do serviço
            $table->string('photo')->nullable(); // Caminho da foto
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
