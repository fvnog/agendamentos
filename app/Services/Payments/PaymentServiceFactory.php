<?php

namespace App\Services\Payments;

use App\Models\StripeAccount;
use App\Services\Payments\StripePaymentService;
use Exception;

class PaymentServiceFactory
{
    public static function create(StripeAccount $stripeAccount)
    {
        switch ($stripeAccount->gateway_name) {
            case 'Stripe':
                return new StripePaymentService($stripeAccount);
            default:
                throw new Exception("Gateway de pagamento não suportado: " . $stripeAccount->gateway_name);
        }
    }
}
