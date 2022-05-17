<?php

namespace Ibehbudov\PaymentGateways\Facades;

use Ibehbudov\PaymentGateways\Exceptions\RequestNotRedirectableException;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;

/**
 * Payment Gateway Facade
 * @see \Ibehbudov\PaymentGateways\PaymentGateway
 *
 * @var float $amount
 * @var string $merchant
 * @var string $locale
 * @var int $currency
 * @var string $description
 * @var array $urls
 * @var array $config
 * @var int $orderId
 * @var BankRequest|null $bankRequest
 * @var bool $isSuccess
 * @method static Payment setPaymentMethod(string $gateway)
 * @method void setConfig(array $config)
 * @method void setAmount(float $amount)
 * @method void setDescription(string $description)
 * @method void setLocale($locale)
 * @method void setBankRequest(BankRequest $bankRequest)
 * @method void setOrderId(int $orderId)
 * @method void setIsSuccess()
 * @method void setTaksitMonth(int $taksitMonth)
 *
 * @method mixed        getConfig($key)
 * @method float        getAmount()
 * @method string       getMerchant()
 * @method string       getLocale()
 * @method int          getCurrency()
 * @method string       getDescription()
 * @method array        getUrls()
 * @method int          getOrderId()
 * @method BankRequest  getBankRequest()
 * @method int          getTaksitMonth()
 *
 * @method bool isSuccess()
 * @method void callback(string $callbackXml)
 * @method void execute()
 * @method void|RedirectResponse redirectToPaymentPage()
 *
 */
class Payment extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }

}


