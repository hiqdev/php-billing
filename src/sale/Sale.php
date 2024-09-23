<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\Exception\ConstraintException;
use hiqdev\php\billing\Exception\InvariantException;
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
     * @var PlanInterface|null
     */
    protected $plan;

    /**
     * @var DateTimeImmutable
     */
    protected $time;

    protected ?DateTimeImmutable $closeTime = null;

    protected ?array $data = null;

    public function __construct(
        $id,
        TargetInterface $target,
        CustomerInterface $customer,
        ?PlanInterface $plan = null,
        ?DateTimeImmutable $time = null,
        ?array $data = null,
    ) {
        $this->id = $id;
        $this->target = $target;
        $this->customer = $customer;
        $this->plan = $plan;
        $this->time = $time ?? new DateTimeImmutable();
        $this->data = $data;
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

    public function getCloseTime(): ?DateTimeImmutable
    {
        return $this->closeTime;
    }

    public function close(DateTimeImmutable $closeTime): SaleInterface
    {
        if ($this->closeTime !== null) {
            throw new InvariantException('Sale is already closed');
        }

        if ($closeTime < $this->time) {
            throw new ConstraintException('Sale close time MUST be greater than open time');
        }

        $this->closeTime = $closeTime;

        return $this;
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

    public function getData(): ?array
    {
        return !empty($this->data) ? $this->data : null;
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    public function cancelClosing(): void
    {
        $this->closeTime = null;
    }
}
