<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\plan\PlanInterface;
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
    /**
     * @return TypeInterface
     */
    public function getType();

    /**
     * @return TargetInterface
     */
    public function getTarget();

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @return QuantityInterface
     */
    public function getQuantity();

    /**
     * @return Money
     */
    public function getSum();

    /**
     * @return PlanInterface
     */
    public function getPlan();

    /**
     * @return ChargeInterface[]
     */
    public function getCharges();
}
