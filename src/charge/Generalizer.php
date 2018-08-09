<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\bill\Bill;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Generalizer implements GeneralizerInterface
{
    public function createBill(ChargeInterface $charge)
    {
        return new Bill(
            null,
            $this->generalizeType($charge),
            $this->generalizeTime($charge),
            $this->generalizeSum($charge),
            $this->generalizeQuantity($charge),
            $this->generalizeCustomer($charge),
            $this->generalizeTarget($charge),
            $this->generalizePlan($charge),
            [$charge]
        );
    }

    public function generalizeType(ChargeInterface $charge)
    {
        return $charge->getPrice()->getType();
    }

    public function generalizeTime(ChargeInterface $charge)
    {
        return $charge->getAction()->getTime()->modify('first day of this month midnight');
    }

    public function generalizeSum(ChargeInterface $charge)
    {
        return $charge->getSum()->negative();
    }

    public function generalizeQuantity(ChargeInterface $charge)
    {
        return $charge->getUsage();
    }

    public function generalizeCustomer(ChargeInterface $charge)
    {
        return $charge->getAction()->getCustomer();
    }

    public function generalizeTarget(ChargeInterface $charge)
    {
        return $charge->getAction()->getTarget();
    }

    public function generalizePlan(ChargeInterface $charge)
    {
        return $charge->getPrice()->getPlan();
    }
}
