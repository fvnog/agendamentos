<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cliente que pagou
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade'); // Horário reservado
            $table->string('type')->default('pix'); // Tipo de pagamento (PIX por padrão)
            $table->decimal('amount', 10, 2); // Valor pago
            $table->string('txid')->unique(); // TXID do pagamento PIX
            $table->json('services'); // Serviços pagos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
