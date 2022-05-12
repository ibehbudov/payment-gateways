<?php

namespace Ibehbudov\PaymentGateways\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getTransactionId()
 * @method static Payment setPaymentMethod(string $gateway)
 * @method Payment setAmount(float $amount)
 *
 * @see \Ibehbudov\PaymentGateways\PaymentGateway
 */
class Payment extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }

}


