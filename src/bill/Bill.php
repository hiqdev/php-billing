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
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
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
    /** @var int|string */
    protected $id;

    /** @var TypeInterface */
    protected $type;

    /** @var DateTimeImmutable */
    protected $time;

    /** @var Money */
    protected $sum;

    /** @var QuantityInterface */
    protected $quantity;

    /** @var CustomerInterface */
    protected $customer;

    /** @var TargetInterface */
    protected $target;

    /** @var BillRequisite */
    protected $requisite;

    /** @var PlanInterface */
    protected $plan;

    /** @var ChargeInterface[] */
    protected $charges = [];

    /** @var BillState */
    protected $state;

    /** @var string */
    protected $comment;

    public function __construct(
                            $id,
        TypeInterface $type,
        DateTimeImmutable $time,
        Money $sum,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        TargetInterface $target = null,
        PlanInterface $plan = null,
        array $charges = [],
        BillState $state = null
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
        $this->state        = $state;
    }

    /**
     * Provides unique string.
     * Can be used to compare or aggregate bills.
     */
    public function getUniqueString(): string
    {
        $parts = [
            'currency'  => $this->sum->getCurrency()->getCode(),
            'buyer'     => $this->customer->getUniqueId(),
            'target'    => $this->target ? $this->target->getUniqueId() : null,
            'type'      => $this->type->getUniqueId(),
            'time'      => $this->time->format('c'),
        ];

        return implode('-', $parts);
    }

    public function calculatePrice()
    {
        $quantity = $this->quantity->getQuantity();

        return $quantity ? $this->sum->divide(sprintf('%.14F', $quantity)) : $this->sum;
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
            throw new CannotReassignException('bill id');
        }
        $this->id = $id;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function getTarget(): ?TargetInterface
    {
        return $this->target;
    }

    public function getRequisite(): ?BillRequisite
    {
        return $this->requisite;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getQuantity(): QuantityInterface
    {
        return $this->quantity;
    }

    public function setQuantity(QuantityInterface $quantity): BillInterface
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSum(): Money
    {
        return $this->sum;
    }

    public function getPlan(): ?PlanInterface
    {
        return $this->plan;
    }

    public function hasCharges(): bool
    {
        return $this->charges !== [];
    }

    /**
     * @return ChargeInterface[]
     */
    public function getCharges(): array
    {
        return $this->charges;
    }

    /**
     * @param ChargeInterface[] $prices
     * @throws \Exception
     */
    public function setCharges(array $charges): self
    {
        if ($this->hasCharges()) {
            throw new CannotReassignException('bill charges');
        }
        $this->charges = $charges;

        return $this;
    }

    public function getState(): ?BillState
    {
        return $this->state;
    }

    public function setFinished(): void
    {
        $this->state = BillState::finished();
    }

    public function isFinished(): ?bool
    {
        return $this->state === null ? null : $this->state->isFinished();
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
