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

    private array $charges = [];

    private string $period = self::PERIOD_MONTH;

    public function __construct(
        CustomerInterface $customer,
        DateTimeImmutable $time,
        Money $balance,
        array $charges = [],
        string $period = self::PERIOD_MONTH
    ) {
        $this->customer = $customer;
        $this->time = $time;
        $this->balance = $balance;
        $this->charges = $charges;
        $this->period = $period;
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

    public function setCharges(array $charges)
    {
        $this->charges = $charges;
    }

    public function getCharges(): array
    {
        return $this->charges;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
