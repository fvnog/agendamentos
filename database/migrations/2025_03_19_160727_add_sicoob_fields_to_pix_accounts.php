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
        Schema::table('pix_account', function (Blueprint $table) {
            $table->string('sicoob_client_id')->nullable();
            $table->string('sicoob_client_secret')->nullable();
            $table->string('sicoob_access_token')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('pix_account', function (Blueprint $table) {
            $table->dropColumn(['sicoob_client_id', 'sicoob_client_secret', 'sicoob_access_token']);
        });
    }
    
};
