<?php

namespace App\Services\Pix;

use App\Models\PixAccount;
use App\Services\Pix\BancoDoBrasilPixService;
use Exception;

class PixServiceFactory
{
    public static function create(PixAccount $pixAccount)
    {
        switch ($pixAccount->bank_name) {
            case 'Banco do Brasil':
                return new BancoDoBrasilPixService($pixAccount);
            default:
                throw new Exception("Banco não suportado: " . $pixAccount->bank_name);
        }
    }
}
