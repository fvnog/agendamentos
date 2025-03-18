<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 🔹 Deleta a tabela antiga se existir
        Schema::dropIfExists('pix_accounts');


    }

    public function down()
    {
        Schema::dropIfExists('pix_account');
    }
};
