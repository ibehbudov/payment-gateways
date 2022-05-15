<?php

namespace Ibehbudov\PaymentGateways\Contracts;

use GuzzleHttp\Exception\GuzzleException;
use Ibehbudov\PaymentGateways\Exceptions\InvalidPaymentArgumentException;
use Psr\Http\Message\ResponseInterface;

interface BankRequestInterface {

    public function run();

    public function setFailed(): void;

    public function failed(): bool;

    public function getBankResponseCode(): string;

    public function setBankResponseCode(string $bankResponseCode): void;

    public function getBankResponseMessage(): string;

    public function setBankResponseMessage(string $bankResponseMessage): void;

    public function getBankResponseData(): array;

    public function setBankResponseData(array $bankResponseData): void;

    public function validateBankResponse(): void;

}
