<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

use DateTimeImmutable;
use hiqdev\php\billing\action\UsageInterval;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\requisite\RequisiteInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Bill Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface BillInterface extends EntityInterface
{
    public function getUniqueString(): string;

    public function getType(): TypeInterface;

    public function getTime(): DateTimeImmutable;

    public function getTarget(): ?TargetInterface;

    public function getRequisite(): ?BillRequisite;

    public function getCustomer(): CustomerInterface;

    public function getQuantity(): QuantityInterface;

    public function getSum(): Money;

    public function getPlan(): ?PlanInterface;

    /**
     * @return ChargeInterface[]
     */
    public function getCharges();

    public function getUsageInterval(): UsageInterval;

    public function setUsageInterval(UsageInterval $usageInterval): void;
}
