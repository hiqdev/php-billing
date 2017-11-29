<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

use DateTime;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * Bill.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Bill implements EntityInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @var Money
     */
    protected $sum;

    /**
     * @var Quantity
     */
    protected $quantity;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Target
     */
    protected $target;

    /**
     * @var Plan
     */
    protected $plan;

    /**
     * @var bool
     */
    protected $isFinished;

    /**
     * @var Charge[]
     */
    protected $charges = [];

    public function __construct(
                    $id,
        Type        $type,
        DateTime    $time,
        Money       $sum,
        Quantity    $quantity,
        Customer    $customer,
        Target      $target = null,
        Plan        $plan = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->time     = $time;
        $this->sum      = $sum;
        $this->quantity = $quantity;
        $this->customer = $customer;
        $this->target   = $target;
        $this->plan     = $plan;
    }

    public function getUniqueId()
    {
        $parts = [
            $this->customer->getUniqueId(),
            $this->sum->getCurrency()->getCode(),
            $this->target ? $this->target->getUniqueId() : null,
            $this->type->getUniqueId(),
            $this->time->format('c'),
            $this->plan ? $this->plan->getUniqueId() : null,
        ];

        return implode('-', array_filter($parts));
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
