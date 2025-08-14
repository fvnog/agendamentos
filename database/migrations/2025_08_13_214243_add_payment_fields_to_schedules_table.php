<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('booked_by')->nullable()->after('client_id'); // quem reservou (pode ser igual client_id)
            $table->unsignedBigInteger('barber_id')->nullable()->after('user_id'); // barbeiro
            $table->json('services_json')->nullable()->after('services'); // backup dos serviços
            $table->decimal('amount_paid', 10, 2)->nullable()->after('services_json');
            $table->string('payment_id')->nullable()->after('amount_paid');
            $table->string('payment_status')->nullable()->after('payment_id');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'booked_by',
                'barber_id',
                'services_json',
                'amount_paid',
                'payment_id',
                'payment_status'
            ]);
        });
    }
};
