<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;

class ReverseRequest extends BankRequest implements BankRequestInterface {

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
                'Request' => [
                    'Operation' => 'Reverse',
                    'Language'  => $payment->getLocale(),
                    'Order'     => [
                        'Merchant'  => $payment->getMerchant(),
                        'OrderID'   => $this->orderId,
                        'Positions' => [
                            'Position' => [
                                'PaymentSubjectType'    => '1',
                                'Quantity'              => '1',
                                'PaymentType'           => '2',
                                'PaymentMethodType'     => '1',
                            ],
                        ],
                    ],
                    'Description'   => $payment->getDescription(),
                    'SessionID'     => $this->sessionId,
                    'TranId'        => '',
                    'Source'        => '1',
                ],
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }
}
