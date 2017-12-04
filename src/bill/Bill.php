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

use DateTimeImmutable;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Bill.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Bill implements BillInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var DateTimeImmutable
     */
    protected $time;

    /**
     * @var Money
     */
    protected $sum;

    /**
     * @var QuantityInterface
     */
    protected $quantity;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var PlanInterface
     */
    protected $plan;

    /**
     * @var bool
     */
    protected $isFinished = false;

    /**
     * @var ChargeInterface[]
     */
    protected $charges = [];

    public function __construct(
                            $id,
        TypeInterface       $type,
        DateTimeImmutable   $time,
        Money               $sum,
        QuantityInterface   $quantity,
        CustomerInterface   $customer,
        TargetInterface     $target = null,
        PlanInterface       $plan = null,
        array               $charges = [],
        bool                $isFinished = false
    ) {
        $this->id           = $id;
        $this->type         = $type;
        $this->time         = $time;
        $this->sum          = $sum;
        $this->quantity     = $quantity;
        $this->customer     = $customer;
        $this->target       = $target;
        $this->plan         = $plan;
        $this->charges      = $charges;
        $this->isFinished   = $isFinished;
    }

    public function getUniqueString()
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

    public function calculatePrice()
    {
        $quantity = $this->quantity->getQuantity();

        return $quantity ? $this->sum->divide($quantity) : $this->sum;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        if ($this->id === $id) {
            return;
        }
        if ($this->id !== null) {
            throw new \Exception('cannot reassign bill id');
        }
        $this->id = $id;
    }

    /**
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return TargetInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return QuantityInterface
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return Money
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return PlanInterface
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return ChargeInterface[]
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * @return bool
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
