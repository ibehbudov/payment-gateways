<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Psr\Http\Message\ResponseInterface;

class CreateOrderRequest extends BankRequest implements BankRequestInterface {

    public bool $responseIsRedirectable = true;

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run()
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request'   =>  [
                    'Operation' =>  'CreateOrder',
                    'Language'  =>  Payment::getLocale(),
                    'Order'     =>  [
                        'OrderType'     =>  'Purchase',
                        'Merchant'      =>  Payment::getMerchant(),
                        'Amount'        =>  Payment::getAmount(),
                        'Currency'      =>  Payment::getCurrency(),
                        'Description'   =>  Payment::getDescription(),
                        'ApproveURL'    =>  Payment::getUrls()['ApproveURL'],
                        'CancelURL'     =>  Payment::getUrls()['CancelURL'],
                        'DeclineURL'    =>  Payment::getUrls()['DeclineURL'],
                    ]
                ],
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }


}
