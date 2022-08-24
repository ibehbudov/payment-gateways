## KapitalBank, AzeriCard payment gateways for Laravel (under development)

### PHP version
* PHP version >= 8.0

### Installation

```php
composer require ibehbudov/payment-gateways
```

### Publish vendor files

```php
php artisan vendor:publish --tag=payment-gateways
```

### Add alias

```php
'Payment'    =>  Ibehbudov\PaymentGateways\Facades\Payment::class
```

## Usage

### KapitalBank usage

* Create order

```php
use Ibehbudov\PaymentGateways\Facades\Payment;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\Requests\CreateOrderRequest;

class PaymentController extends Controller
{
    public function createOrder()
    {
        $payment = Payment::setPaymentMethod(KapitalBank::class);

        $payment->setAmount(13.45);
        $payment->setDescription("Description");
        $payment->setLocale(App::getLocale());
        $payment->setBankRequest(new CreateOrderRequest());
        $payment->getBankRequest()->exceptionWhenFailed(false);
        
        $payment->execute();

        $orderId = $payment->getOrderId();

        $redirectUrl = $payment->getBankRequest()->getRedirectUrl();

        return $payment->redirectToPaymentPage();
    }
}
```

###
* Refund

```php
$payment = Payment::setPaymentMethod(KapitalBank::class);

$payment->setBankRequest(new RefundOrderRequest(
    orderId: 44516491,
    sessionId: "9FF55469ADFA786A0E082708E4406579",
));

$payment->setAmount(10);
$payment->setLocale(App::getLocale());
$payment->setDescription("Refund");
$payment->getBankRequest()->exceptionWhenFailed(false);

$payment->execute();

if($payment->getBankRequest()->failed()) {
    echo "request is failed: ";
}
else {
    echo "request is success";
}
```

###
* Callback
```php
$payment = Payment::setPaymentMethod(KapitalBank::class);

$payment->callback($request->post('xmlmsg'));

if($payment->isSuccess()) {
    // success code
    echo "success";
}
```

###
* PreAuth Order

```php
$payment = Payment::setPaymentMethod(KapitalBank::class);

$payment->setBankRequest(new PreAuthRequest());
$payment->setAmount(10);
$payment->setDescription("Description text");
$payment->getBankRequest()->exceptionWhenFailed(true);

$payment->execute();

$orderId = $payment->getOrderId();

return $payment->redirectToPaymentPage();
```

###
* Reverse Order

```php
$payment = Payment::setPaymentMethod(KapitalBank::class);

$payment->setBankRequest(new ReverseRequest(
    orderId: 44516491,
    sessionId: "9FF55469ADFA786A0E082708E4406579",
));
$payment->setAmount(10);
$payment->setDescription("Description text");
$payment->getBankRequest()->exceptionWhenFailed(false);
$payment->execute();

if($payment->getBankRequest()->success()) {
    // code
}

if($payment->getBankRequest()->failed()) {
    // code
}
```

### Requests list
* CardRegistrationRequest
* CompletionRequest
* CreateOrderRequest
* CreateOrderWithCardUIDRequest (Not working properly)
* OrderStatusRequest
* PreAuthRequest
* RefundOrderRequest
* ReverseRequest
* TaksitRequest





