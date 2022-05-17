<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;

class CompletionRequest extends BankRequest implements BankRequestInterface {

    public function __construct(
        public int $orderId,
        public string $sessionId,
    )
    {
        parent::__construct();
    }

    public function run(PaymentGatewayInterface $payment)
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request'   =>  [
                    'Operation' => 'Completion',
                    'Language'  => $payment->getLocale(),
                    'Order'     => [
                        'Merchant'  => $payment->getMerchant(),
                        'OrderID'   => $this->orderId,
                    ],
                    'SessionID'     => $this->sessionId,
                    'Amount'        => $payment->getAmount(),
                    'Description'   => $payment->getDescription(),
                ],
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }
}
