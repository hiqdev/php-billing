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

    protected mixed $data = null;

    public function __construct(
        $id,
        TargetInterface $target,
        CustomerInterface $customer,
        ?PlanInterface $plan = null,
        ?DateTimeImmutable $time = null,
        mixed $data = null,
    ) {
        $this->id = $id;
        $this->target = $target;
        $this->customer = $customer;
        $this->plan = $plan;
        $this->time = $time ?? new DateTimeImmutable();
        $this->data = $this->setData($data);
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

    public function close(DateTimeImmutable $closeTime): void
    {
        if ($this->closeTime !== null) {
            throw new InvariantException('Sale is already closed');
        }

        if ($closeTime < $this->time) {
            throw new ConstraintException('Sale close time MUST be greater than open time');
        }

        $this->closeTime = $closeTime;
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

    public function setData(mixed $data = null)
    {
        if (is_null($data) || empty($data)) {
            return ;
        }
        if (is_string($data)) {
            $this->data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            return ;
        }

        if (is_object($data)) {
            $this->data = json_decode(json_encode($data), true, 512, JSON_THROW_ON_ERROR);
        }

        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
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
