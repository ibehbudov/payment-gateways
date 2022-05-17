<?php

namespace Ibehbudov\PaymentGateways;

use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Exceptions\MissingPaymentConfigException;
use Ibehbudov\PaymentGateways\Exceptions\UndefinedPaymentMethodException;
use Ibehbudov\PaymentGateways\Exceptions\UnknownPaymentMerchantException;
use Illuminate\Support\Str;

class PaymentGateway {

    /**
     * @var PaymentGatewayInterface
     */
    public PaymentGatewayInterface $paymentGateway;

    public bool $refundable;

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return Str::random();
    }

    /**
     * @param string $paymentGateway
     * @throws UnknownPaymentMerchantException
     */
    public function setPaymentMethod(string $paymentGateway)
    {
        $gateway = new $paymentGateway;

        if(! $gateway instanceof PaymentGatewayInterface) {
            throw new UnknownPaymentMerchantException("Payment class must implement from PaymentGatewayInterface");
        }

        $this->paymentGateway = $gateway;

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws UndefinedPaymentMethodException
     */
    public function __call(string $name, array $arguments)
    {
        if(! method_exists($this->paymentGateway, $name) ){
            throw new UndefinedPaymentMethodException("Undefined Payment method '{$name}' on class " . $this->paymentGateway::class);
        }

        return $this->paymentGateway->{$name}(...$arguments);
    }
}
