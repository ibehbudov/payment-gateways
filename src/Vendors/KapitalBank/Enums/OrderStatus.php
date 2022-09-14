<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Enums;

class OrderStatus
{
    const CREATED    = 'CREATED';
    const ON_LOCK    = 'ON-LOCK';
    const ON_PAYMENT = 'ON-PAYMENT';
    const APPROVED   = 'APPROVED';
    const CANCELED   = 'CANCELED';
    const DECLINED   = 'DECLINED';
}
