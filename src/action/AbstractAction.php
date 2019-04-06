<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use DateTimeImmutable;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\price\PriceInterface;
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
abstract class AbstractAction implements ActionInterface, EntityInterface
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

    /**
     * @param TypeInterface $type
     * @param TargetInterface $target
     * @param QuantityInterface $quantity
     * @param CustomerInterface $customer
     * @param SaleInterface $sale
     * @param DateTimeImmutable $time
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
        ActionInterface $parent = null
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

    public function createSubaction(CustomerInterface $customer)
    {
        return new static(null, $this->type, $this->target, $this->quantity, $customer, $this->time, null, $this->state, $this);
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
            throw new \Exception('cannot reassign action id');
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
            throw new \Exception('cannot reassign sale for action');
        }
        $this->sale = $sale;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
