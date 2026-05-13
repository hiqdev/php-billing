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
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\plan\PlanInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Statement implements \JsonSerializable
{
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';

    public function __construct(
        private readonly CustomerInterface $customer,
        private readonly DateTimeImmutable $time,
        private readonly DateTimeImmutable $month,
        private readonly Money $balance,
        private readonly Money $total,
        private readonly Money $payment,
        private readonly Money $amount,
        private array $bills = [],
        private readonly string $period = self::PERIOD_MONTH,
        private array $plans = []
    ) {
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

    /**
     * @param BillInterface[]
     */
    public function setBills(array $bills): void
    {
        $this->bills = $bills;
    }

    /**
     * @return BillInterface[]
     */
    public function getBills(): array
    {
        return $this->bills ?? [];
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

    /**
     * @param PlanInterface[]
     */
    public function setPlans(array $plans): void
    {
        $this->plans = $plans;
    }

    /**
     * @return PlanInterface[]
     */
    public function getPlans(): array
    {
        return $this->plans ?? [];
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
