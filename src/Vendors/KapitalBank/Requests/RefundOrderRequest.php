<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Psr\Http\Message\ResponseInterface;

class RefundOrderRequest extends BankRequest implements BankRequestInterface {

    public function __construct(
        public int $orderId,
        public string $sessionId,
    )
    {
        parent::__construct();
    }

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run()
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request' => [
                    'Operation' => 'Refund',
                    'Language'  => Payment::getLocale(),
                    'Order'     => [
                        'Merchant'  => Payment::getMerchant(),
                        'OrderID'   => $this->orderId,
                        'Positions' => [
                            'Position' => [
                                'PaymentSubjectType'    => '1',
                                'Quantity'              => '1',
                                'Price'                 => '1',
                                'Tax'                   => '1',
                                'Text'                  => 'name position',
                                'PaymentType'           => '2',
                                'PaymentMethodType'     => '1',
                            ],
                        ],
                    ],
                    'Description'   => Payment::getDescription(),
                    'SessionID'     => $this->sessionId,
                    'Refund'        => [
                        'Amount'    => Payment::getAmount(),
                        'Currency'  => Payment::getCurrency(),
                        'WithFee'   => 'false',
                    ],
                    'Source'    => '1',
                ]
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }
}
