<?php

namespace Ibehbudov\PaymentGateways\Contracts;

interface PaymentGatewayInterface {

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void;

    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void;

    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param string $description
     */
    public function setDescription(string $description): void;

    /**
     * @return string
     */
    public function getDescription(): ?string;
}
