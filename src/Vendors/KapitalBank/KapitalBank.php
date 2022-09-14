<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank;

use Ibehbudov\PaymentGateways\Contracts\PaymentGatewayInterface;
use Ibehbudov\PaymentGateways\Exceptions\InvalidTaksitMonthException;
use Ibehbudov\PaymentGateways\Exceptions\MissingPaymentConfigException;
use Ibehbudov\PaymentGateways\Exceptions\RequestNotRedirectableException;
use Ibehbudov\PaymentGateways\Exceptions\UnhandledBankResponseException;
use Ibehbudov\PaymentGateways\Library\XmlConverter;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Enums\OrderStatus;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests\OrderStatusRequest;
use Illuminate\Support\Str;

class KapitalBank implements PaymentGatewayInterface {

    /**
     * @var float
     */
    public float $amount = 0.00;

    /**
     * @var string|null
     */
    public ?string $merchant;

    /**
     * @var string
     */
    public string $locale = 'AZ';

    /**
     * @var int
     */
    public int $currency = 944;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @var array
     */
    public array $urls = [];

    /**
     * @var array
     */
    public array $config;

    /**
     * @var int
     */
    public int $orderId;

    /**
     * @var BankRequest
     */
    public BankRequest|null $bankRequest = null;

    /**
     * @var bool
     */
    public bool $isSuccess = false;

    /**
     * @var int
     */
    public int $taksitMonth;

    /**
     * @throws MissingPaymentConfigException
     */
    public function __construct()
    {
        $this->setConfig();

        $this->merchant = $this->getConfig('merchant');

        $this->setUrls([
            'ApproveURL'    =>  $this->getConfig('ApproveURL'),
            'CancelURL'     =>  $this->getConfig('CancelURL'),
            'DeclineURL'    =>  $this->getConfig('DeclineURL'),
        ]);
    }


    /**
     * @return array
     */
    public function getConfig($key): mixed
    {
        if(! array_key_exists($key, $this->config)) {
            throw new MissingPaymentConfigException("Key '{$key}' is missing on KapitalBank payment config. Please run vendor publish command again");
        }

        return $this->config[$key];
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = []): void
    {
        $this->config = config('payment-gateways.kapital');
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount * 100;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getMerchant(): ?string
    {
        return $this->merchant;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return Str::upper($this->locale);
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return int
     */
    public function getCurrency(): int
    {
        return $this->currency;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * @param array $urls
     */
    public function setUrls(array $urls): void
    {
        $this->urls = $urls;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return BankRequest
     */
    public function getBankRequest(): BankRequest
    {
        if(is_null($this->bankRequest)) {
            $this->bankRequest = new (BankRequest::defaultRequest());
        }

        return $this->bankRequest;
    }

    /**
     * @param BankRequest $bankRequest
     */
    public function setBankRequest(BankRequest $bankRequest): void
    {
        $this->bankRequest = $bankRequest;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param bool $isSuccess
     */
    public function setIsSuccess(): void
    {
        $this->isSuccess = true;
    }


    /**
     * @return int
     */
    public function getTaksitMonth(): int
    {
        return $this->taksitMonth;
    }

    /**
     * @param int $taksitMonth
     */
    public function setTaksitMonth(int $taksitMonth): void
    {
        if(! in_array($taksitMonth, $this->getConfig('taksit_month'))) {
            throw new InvalidTaksitMonthException("Invalid taksit month. Please check config file");
        }

        $this->taksitMonth = $taksitMonth;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws RequestNotRedirectableException
     */
    public function redirectToPaymentPage()
    {
        if($this->getBankRequest()->responseIsRedirectable === false) {
            throw new RequestNotRedirectableException("Request not redirectable");
        }

        if(! $this->getBankRequest()->failed()) {
            return redirect()->to(
                $this->getBankRequest()->getRedirectUrl()
            );
        }
    }

    /**
     * Execute payment request
     */
    public function execute(): void
    {
        $client = $this->getBankRequest()->run($this);

        try {
            $responseArray = XmlConverter::xmlToArray($client->getBody());

            $this->getBankRequest()->setBankResponseData($responseArray);

        }catch (\Exception $exception) {
            if($this->getBankRequest()->exceptionWhenFailed === true) {
                throw new UnhandledBankResponseException($exception->getMessage());
            }
            else {
                $this->getBankRequest()->setFailed();
            }
        }

        $this->getBankRequest()->validateBankResponse();

        $this->getBankRequest()->exception();
    }

    /**
     * @param string $callbackXml
     * @throws UnhandledBankResponseException
     */
    public function callback(string $callbackXml)
    {
        $arrayResponse = XmlConverter::xmlToArray($callbackXml);

        if($arrayResponse['OrderStatus'] === OrderStatus::APPROVED) {
            $this->setBankRequest(
                new OrderStatusRequest(
                    $arrayResponse['OrderID'],
                    $arrayResponse['SessionID']
                )
            );

            $this->execute();
        }
    }

}
