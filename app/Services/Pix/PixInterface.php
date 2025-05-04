<?php 

namespace App\Services\Pix;

interface PixInterface
{
    /**
     * Cria um pagamento Pix.
     *
     * @param float $amount Valor do pagamento
     * @param int $userId ID do usuário que está recebendo o pagamento
     * @return mixed Resposta da API Pix do banco correspondente
     */
    public function createPayment($amount, $userId);

    /**
     * Consulta o status de um pagamento Pix.
     *
     * @param string $txid ID da transação Pix
     * @return mixed Resposta com o status do pagamento
     */
    public function checkPaymentStatus($txid);
}

