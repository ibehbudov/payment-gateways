<?php

namespace Ibehbudov\PaymentGateways\Contracts;

use GuzzleHttp\Exception\GuzzleException;
use Ibehbudov\PaymentGateways\Exceptions\InvalidPaymentArgumentException;

interface BankRequestInterface {

    public function run(): void;

    public function isFailed(): bool;

    public function failed(): void;

    public function getBankResponseCode(): string;

    public function setBankResponseCode(string $bankResponseCode): void;

    public function getBankResponseMessage(): string;

    public function setBankResponseMessage(string $bankResponseMessage): void;

    public function getBankResponseData(): array;

    public function setBankResponseData(array $bankResponseData): void;

    public function validateBankResponse(): void;

}
