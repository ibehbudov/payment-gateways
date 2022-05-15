<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank;

use Exception;
use Ibehbudov\PaymentGateways\Exceptions\InvalidCallbackMessageException;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Enums\OrderStatus;
use Illuminate\Support\Str;

class Callback {

    public function __construct(public array $callbackArray, public ?OrderStatus $status = null)
    {
        try {
            $callbackStatus = Str::lower($callbackArray['OrderStatus']);

            $this->status = match ($callbackStatus){
                'created'       =>  OrderStatus::CREATED,
                'on-lock'       =>  OrderStatus::ON_LOCK,
                'on-payment'    =>  OrderStatus::ON_PAYMENT,
                'approved'      =>  OrderStatus::APPROVED,
                'canceled'      =>  OrderStatus::CANCELED,
                'declined'      =>  OrderStatus::DECLINED,
            };
        }
        catch (Exception $exception){
            throw new InvalidCallbackMessageException("Invalid bank callback response");
        }
    }

    public static function getOrderStatus(string $callback)
    {
        return match ($callback){
            'created'       =>  OrderStatus::CREATED,
            'on-lock'       =>  OrderStatus::ON_LOCK,
            'on-payment'    =>  OrderStatus::ON_PAYMENT,
            'approved'      =>  OrderStatus::APPROVED,
            'canceled'      =>  OrderStatus::CANCELED,
            'declined'      =>  OrderStatus::DECLINED,
        };
    }
}
