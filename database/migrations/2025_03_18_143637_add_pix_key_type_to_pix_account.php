<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pix_account', function (Blueprint $table) {
            $table->string('pix_key_type')->after('pix_key')->default('aleatoria'); // Tipo da chave Pix
        });
    }

    public function down()
    {
        Schema::table('pix_account', function (Blueprint $table) {
            $table->dropColumn('pix_key_type');
        });
    }
};
