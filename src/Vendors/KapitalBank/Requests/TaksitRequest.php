<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Psr\Http\Message\ResponseInterface;

class TaksitRequest extends BankRequest implements BankRequestInterface {

    public bool $responseIsRedirectable = true;

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run(PaymentGatewayInterface $payment)
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request'   =>  [
                    'Operation' =>  'CreateOrder',
                    'Language'  =>  $payment->getLocale(),
                    'Order'     =>  [
                        'OrderType'     =>  'Purchase',
                        'Merchant'      =>  $payment->getMerchant(),
                        'Amount'        =>  $payment->getAmount(),
                        'Currency'      =>  $payment->getCurrency(),
                        'Description'   =>  "TAKSIT=" . $payment->getTaksitMonth(),
                        'ApproveURL'    =>  $payment->getUrls()['ApproveURL'],
                        'CancelURL'     =>  $payment->getUrls()['CancelURL'],
                        'DeclineURL'    =>  $payment->getUrls()['DeclineURL'],
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
