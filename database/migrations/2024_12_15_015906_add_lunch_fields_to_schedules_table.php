<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->time('lunch_start')->nullable(); // Início do intervalo de almoço
            $table->time('lunch_end')->nullable(); // Fim do intervalo de almoço
            $table->boolean('is_lunch_break')->default(false); // Marcar se é um horário de almoço
        });
    }
    
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('lunch_start');
            $table->dropColumn('lunch_end');
            $table->dropColumn('is_lunch_break');
        });
    }
    
};
