<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Psr\Http\Message\ResponseInterface;

class CardRegistrationRequest extends BankRequest implements BankRequestInterface{

    public bool $responseIsRedirectable = true;

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run(PaymentGatewayInterface $payment)
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request' => [
                    'Operation' => 'CreateOrder',
                    'Language'  => $payment->getLocale(),
                    'Order'     => [
                        'OrderType'         => 'Purchase',
                        'Merchant'          =>  $payment->getMerchant(),
                        'Amount'            =>  $payment->getAmount(),
                        'Currency'          =>  $payment->getCurrency(),
                        'Description'       =>  $payment->getDescription(),
                        'ApproveURL'        =>  $payment->getUrls()['ApproveURL'],
                        'CancelURL'         =>  $payment->getUrls()['CancelURL'],
                        'DeclineURL'        =>  $payment->getUrls()['DeclineURL'],
                        'CardRegistration'  => [
                            'RegisterCardOnSuccess' => 'true',
                            'CheckRegisterCardOn'   => 'true',
                            'SaveCardUIDToOrder'    => 'true',
                        ],
                        'AddParams' => [
                            'CustomFields'  => [
                                'Param' => [
                                    '@value'        => '',
                                    '@attributes'   => [
                                        'name'  => 'Attention',
                                        'title' => __('payment-gateways.kapital.card_registration_attention'),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }


}
