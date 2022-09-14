<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests;

use Ibehbudov\PaymentGateways\Contracts\BankRequestInterface;
use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Enums\OrderStatus;
use Psr\Http\Message\ResponseInterface;

class OrderStatusRequest extends BankRequest implements BankRequestInterface {

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
    public function run(PaymentGatewayInterface $payment)
    {
        $xml = XmlConverter::arrayToXml(
            array: [
                'Request' => [
                    'Operation' => 'GetOrderStatus',
                    'Language'  => $payment->getLocale(),
                    'Order'     => [
                        'Merchant'  => $payment->getMerchant(),
                        'OrderID'   => $this->orderId,
                    ],
                    'SessionID' => $this->sessionId,
                ]
            ],
            rootElement: 'TKKPG',
            xmlEncoding: "UTF-8");

        return $this->httpClient->post($this->endpoint, [
            'body'  =>  $xml
        ]);
    }

    public function validateBankResponse(): void
    {
        parent::validateBankResponse();

        if(! $this->failed()) {

            $status = $this->getBankResponseData()['Response']['Order']['OrderStatus'];

            if($status === OrderStatus::APPROVED) {
                Payment::setIsSuccess();
            }
        }
    }
}
