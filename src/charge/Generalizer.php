<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\UsageInterval;
use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Generalizer implements GeneralizerInterface
{
    public function createBill(ChargeInterface $charge): BillInterface
    {
        $bill = new Bill(
            null,
            $this->generalizeType($charge),
            $this->generalizeTime($charge),
            $this->generalizeSum($charge),
            $this->generalizeQuantity($charge),
            $this->generalizeCustomer($charge),
            $this->generalizeTarget($charge),
            $this->generalizePlan($charge),
            [$charge],
        );

        $bill->setUsageInterval($this->generalizeUsageInterval($charge));

        return $bill;
    }

    public function generalizeType(ChargeInterface $charge): TypeInterface
    {
        return $charge->getType();
    }

    public function generalizeTime(ChargeInterface $charge): \DateTimeImmutable
    {
        return $charge->getAction()->getTime();
    }

    public function generalizeSum(ChargeInterface $charge): Money
    {
        return $charge->getSum()->negative();
    }

    public function generalizeQuantity(ChargeInterface $charge): QuantityInterface
    {
        return $charge->getUsage();
    }

    public function generalizeCustomer(ChargeInterface $charge): CustomerInterface
    {
        return $charge->getAction()->getCustomer();
    }

    public function generalizeTarget(ChargeInterface $charge): TargetInterface
    {
        return $charge->getTarget();
    }

    public function generalizePlan(ChargeInterface $charge): ?PlanInterface
    {
        return $charge->getPrice()->getPlan();
    }

    public function specializeType(TypeInterface $first, TypeInterface $other): TypeInterface
    {
        return $first;
    }

    public function specializeTarget(TargetInterface $first, TargetInterface $other): TargetInterface
    {
        return $first;
    }

    private function generalizeUsageInterval(ChargeInterface $charge): UsageInterval
    {
        return $charge->getAction()->getUsageInterval();
    }
}
