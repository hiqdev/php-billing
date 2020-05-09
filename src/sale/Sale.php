<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;

/**
 * Sale.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Sale implements SaleInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var PlanInterface
     */
    protected $plan;

    /**
     * @var DateTimeImmutable
     */
    protected $time;

    public function __construct(
                            $id,
        TargetInterface $target,
        CustomerInterface $customer,
        PlanInterface $plan,
        DateTimeImmutable $time = null
    ) {
        $this->id = $id;
        $this->target = $target;
        $this->customer = $customer;
        $this->plan = $plan;
        $this->time = $time;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getPlan()
    {
        return $this->plan;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function hasId()
    {
        return $this->id !== null;
    }

    public function setId($id)
    {
        if ((string) $this->id === (string) $id) {
            return;
        }
        if ($this->hasId()) {
            throw new CannotReassignException('sale id');
        }
        $this->id = $id;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
