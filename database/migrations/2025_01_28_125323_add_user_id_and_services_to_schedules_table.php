<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAndServicesToSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(); // ID do cliente
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade'); // Relacionamento com users
            $table->json('services')->nullable(); // Serviços escolhidos
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'services']);
        });
    }
}
