<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use DateInterval;
use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;

/**
 * Chargeable Action.
 *
 * @see ActionInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractAction implements \JsonSerializable, ActionInterface
{
    /** @var int */
    protected $id;

    /** @var TypeInterface */
    protected $type;

    /** @var TargetInterface */
    protected $target;

    /** @var QuantityInterface */
    protected $quantity;

    /** @var CustomerInterface */
    protected $customer;

    /** @var DateTimeImmutable */
    protected $time;

    /** @var SaleInterface */
    protected $sale;

    /** @var ActionState */
    protected $state;

    /** @var ActionInterface */
    protected $parent;

    protected float $fraction_of_month;

    /**
     * @param SaleInterface $sale
     * @param ActionInterface $parent
     */
    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        DateTimeImmutable $time,
        SaleInterface $sale = null,
        ActionState $state = null,
        ActionInterface $parent = null,
        $fractionOfMonth = 0.0
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->target   = $target;
        $this->quantity = $quantity;
        $this->customer = $customer;
        $this->time     = $time;
        $this->sale     = $sale;
        $this->state    = $state;
        $this->parent   = $parent;
        $this->fraction_of_month = $fractionOfMonth;
    }

    /**
     * Provides unique string.
     * Can be used to compare or aggregate actions.
     */
    public function getUniqueString(): string
    {
        $parts = [
            'buyer'     => $this->customer->getUniqueId(),
            'target'    => $this->target ? $this->target->getUniqueId() : null,
            'type'      => $this->type->getUniqueId(),
            'time'      => $this->time->format('c'),
        ];

        return implode('-', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(): TargetInterface
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity(): QuantityInterface
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getSale(): ?SaleInterface
    {
        return $this->sale;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(DateTimeImmutable $time)
    {
        $this->time = $time;
    }

    public function getState(): ?ActionState
    {
        return $this->state;
    }

    public function setFinished(): void
    {
        $this->state = ActionState::finished();
    }

    public function isFinished(): ?bool
    {
        return $this->state === null ? null : $this->state->isFinished();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?ActionInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return $this->parent !== null;
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
            throw new CannotReassignException("action id old:{$this->id} new: $id");
        }
        $this->id = $id;
    }

    public function hasSale()
    {
        return $this->sale !== null;
    }

    public function setSale(SaleInterface $sale)
    {
        if ($this->hasSale()) {
            throw new CannotReassignException('action sale');
        }
        $this->sale = $sale;
    }

    public function getFractionOfMonth(): float
    {
        return $this->fraction_of_month;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    public function getUsageInterval(): UsageInterval
    {
        if ($this->getSale()?->getTime() === null) {
            return UsageInterval::wholeMonth($this->getTime());
        }

        return UsageInterval::withinMonth(
            $this->getTime(),
            $this->getSale()->getTime(),
            $this->getSale()->getCloseTime(),
            $this->getFractionOfMonth()
        );
    }
}
