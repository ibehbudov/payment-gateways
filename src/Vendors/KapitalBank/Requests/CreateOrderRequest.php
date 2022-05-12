<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Exceptions\InvalidPaymentArgumentException;
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;

class CreateOrderRequest extends BankRequest implements BankRequestInterface {

    public bool $responseIsRedirectable = true;

    /**
     * @throws InvalidPaymentArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run(): void
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

        $response = $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);

        $responseArray = XmlConverter::xmlToArray($response->getBody() . "");

        $this->setBankResponseData($responseArray);

        $this->validateBankResponse();
    }
}
