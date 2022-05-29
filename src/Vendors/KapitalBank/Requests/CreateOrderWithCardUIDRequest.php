<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Psr\Http\Message\ResponseInterface;

class CreateOrderWithCardUIDRequest extends BankRequest implements BankRequestInterface {

    public bool $responseIsRedirectable = true;

    public function __construct(
        public int $orderID,
        public string $sessionID,
        public string $cardUID
    )
    {
        parent::__construct();
    }

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run(PaymentGatewayInterface $payment)
    {
        $array = [
            'Request'   =>  [
                'Operation' =>  'Purchase',
                'Amount'    =>  $payment->getAmount(),
                'CardUID'   =>  $this->cardUID,
                'SessionID' =>  $this->sessionID,
                'Currency'      =>  $payment->getCurrency(),
                'Order'     =>  [
                    'Merchant'      =>  $payment->getMerchant(),
                    'OrderID'       =>  $this->orderID,
                ],
            ],
        ];

        $xml = XmlConverter::arrayToXml(
            array: $array,
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8"
        );

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }


}
