<?php
declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\statement;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Statement implements \JsonSerializable
{
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';

    private CustomerInterface $customer;

    private DateTimeImmutable $time;

    private Money $balance;

    private array $bills = [];

    private string $period = self::PERIOD_MONTH;

    private DateTimeImmutable $month;

    private Money $total;

    private Money $payment;

    private Money $amount;

    public function __construct(
        CustomerInterface $customer,
        DateTimeImmutable $time,
        Money $balance,
        array $bills = [],
        string $period = self::PERIOD_MONTH,
        DateTimeImmutable $month,
        Money $total,
        Money $payment,
        Money $amount
    ) {
        $this->customer = $customer;
        $this->time = $time;
        $this->balance = $balance;
        $this->bills = $bills;
        $this->period = $period;
        $this->month = $month;
        $this->total = $total;
        $this->payment = $payment;
        $this->amount = $amount;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function getBalace(): Money
    {
        return $this->balance;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setBills(array $bills)
    {
        $this->bills = $bills;
    }

    public function getBills(): array
    {
        return $this->bills;
    }

    public function getMonth(): DateTimeImmutable
    {
        return $this->month;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    public function getPayment(): Money
    {
        return $this->payment;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
