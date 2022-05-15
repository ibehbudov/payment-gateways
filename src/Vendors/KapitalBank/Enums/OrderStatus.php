<?php

namespace Ibehbudov\PaymentGateways\Vendors\KapitalBank\Enums;

enum OrderStatus: string
{
    case CREATED    = 'CREATED';
    case ON_LOCK    = 'ON-LOCK';
    case ON_PAYMENT = 'ON-PAYMENT';
    case APPROVED   = 'APPROVED';
    case CANCELED   = 'CANCELED';
    case DECLINED   = 'DECLINED';
}
