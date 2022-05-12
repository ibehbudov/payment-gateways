<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank;

use Ibehbudov\PaymentGateways\Exceptions\InvalidPaymentArgumentException;
use Ibehbudov\PaymentGateways\Exceptions\RedirectUrlNotFoundException;
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\HttpPaymentClient;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests\CreateOrderRequest;
use Illuminate\Support\Str;

class BankRequest {

    /**
     * @var HttpPaymentClient
     */
    public HttpPaymentClient $httpClient;

    /**
     * Service ling
     * @var string
     */
    public string $endpoint = 'https://e-commerce.kapitalbank.az:5443/Exec';

    /**
     * @var string
     */
    public string $bankResponseCode;

    /**
     * @var string
     */
    public string $bankResponseMessage;

    /**
     * @var array
     */
    public array $bankResponseData = [];

    /**
     * @var bool
     */
    public bool $failed = false;

    /**
     * @var bool
     */
    public bool $responseIsRedirectable = false;

    /**
     * @var string
     */
    public string $redirectUrl = '';

    /**
     * @var bool
     */
    public bool $exceptionWhenFailed;

    /**
     * @var array|string[]
     */
    protected array $responseCodes = [
        "SUCCESS"               =>  "00",
        "INVALID_FIELD_FORMAT"  =>  "30",
        "SHOP_HAS_NO_ACCESS"    =>  "10",
        "INVALID_OPERATION"     =>  "54",
        "SYSTEM_ERROR"          =>  "96",
    ];

    /**
     * Bank request constructor
     */
    public function __construct()
    {
        $this->httpClient = new HttpPaymentClient([
            'curl'  =>  [
                CURLOPT_SSLKEY          =>  Payment::getConfig('key'),
                CURLOPT_SSLCERT         =>  Payment::getConfig('ssl_cert'),
                CURLOPT_SSL_VERIFYHOST  =>  false,
                CURLOPT_SSL_VERIFYPEER  =>  false,
            ],
        ]);
    }

    /**
     * @return string
     */
    public static function defaultRequest()
    {
        return CreateOrderRequest::class;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->failed;
    }

    /**
     * Set transaction is failed
     */
    public function failed(): void
    {
        $this->failed = true;
    }

    /**
     * @return string
     */
    public function getBankResponseCode(): string
    {
        return $this->bankResponseCode;
    }

    /**
     * @param string $bankResponseCode
     */
    public function setBankResponseCode(string $bankResponseCode): void
    {
        $this->bankResponseCode = $bankResponseCode;
    }

    /**
     * @return string
     */
    public function getBankResponseMessage(): string
    {
        return $this->bankResponseMessage;
    }

    /**
     * @param string $bankResponseMessage
     */
    public function setBankResponseMessage(string $bankResponseMessage): void
    {
        $this->bankResponseMessage = $bankResponseMessage;
    }

    /**
     * @return array
     */
    public function getBankResponseData(): array
    {
        return $this->bankResponseData;
    }

    /**
     * @param array $bankResponseData
     */
    public function setBankResponseData(array $bankResponseData): void
    {
        $this->bankResponseData = $bankResponseData;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     *v alidateBankResponse
     */
    public function validateBankResponse(): void
    {
        $data = $this->getBankResponseData();

        $responseCode = ! empty($data['Response']['Status']) && in_array($data['Response']['Status'], $this->responseCodes) ?
            $data['Response']['Status'] :
            $this->responseCodes["SYSTEM_ERROR"];

        $responseCodeKey = array_search($responseCode, $this->responseCodes);

        $this->setBankResponseCode($responseCodeKey);
        $this->setBankResponseMessage(__('payment-gateways.kapital.' . Str::lower($responseCodeKey)));

        if($responseCode !== $this->responseCodes["SUCCESS"]) {
            $this->failed();
            $this->exception();
        }

        $this->searchForRedirectLink();
    }

    /**
     * @throws InvalidPaymentArgumentException
     */
    public function exception()
    {
        if($this->isFailed() && $this->exceptionWhenFailed === true) {
            throw new InvalidPaymentArgumentException(
                $this->getBankResponseCode() . ": " .
                $this->getBankResponseMessage()
            );
        }
    }

    /**
     * @param bool $bool
     */
    public function exceptionWhenFailed(bool $bool = true)
    {
        $this->exceptionWhenFailed = $bool;
    }

    /**
     * @throws RedirectUrlNotFoundException
     */
    public function searchForRedirectLink()
    {
        if($this->responseIsRedirectable === true && !$this->isFailed()) {

            $order = $this->getBankResponseData()['Response']['Order'] ?? [];

            if(empty($order['URL'])) {
                throw new RedirectUrlNotFoundException("Redirect URL not found in bank response or request is not redirectable");
            }

            $this->setRedirectUrl($order['URL'] . '?' . http_build_query([
                    'SessionID' => $order['SessionID'],
                    'OrderID'   => $order['OrderID'],
                ]));
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        return redirect()->to($this->getRedirectUrl());
    }
}
