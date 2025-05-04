<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pix_account', function (Blueprint $table) {
            $table->string('bb_client_id')->nullable()->after('pix_key_type');
            $table->string('bb_client_secret')->nullable()->after('bb_client_id');
            $table->string('bb_gw_app_key')->nullable()->after('bb_client_secret');
        });
    }

    public function down()
    {
        Schema::table('pix_account', function (Blueprint $table) {
            $table->dropColumn(['bb_client_id', 'bb_client_secret', 'bb_gw_app_key']);
        });
    }
};
