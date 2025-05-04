<?php

namespace App\Services\Pix;

use App\Models\PixAccount;
use Exception;

class PixServiceFactory
{
    public static function create(PixAccount $pixAccount)
    {
        switch ($pixAccount->bank_name) {
            case 'Banco do Brasil':
                return new BancoDoBrasilPixService($pixAccount);
            case 'Sicoob':
                return new SicoobPixService($pixAccount);
            default:
                throw new Exception("Banco não suportado: " . $pixAccount->bank_name);
        }
    }
}
