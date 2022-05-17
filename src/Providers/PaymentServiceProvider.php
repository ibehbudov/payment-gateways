<?php

namespace Ibehbudov\PaymentGateways\Providers;

use Ibehbudov\PaymentGateways\PaymentGateway;
use Ibehbudov\PaymentGateways\Vendors\KapitalBank\BankRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('payment', PaymentGateway::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../certificates'                =>  storage_path('app/certificates'),
            __DIR__ . '/../config/payment-gateways.php' =>  config_path('payment-gateways.php'),
            __DIR__ . '/../lang/'                       =>  lang_path(App::getLocale())
        ], 'payment-gateways');
    }
}
