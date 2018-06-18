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

    /** @var SaleInterface */
    protected $sale;

    /** @var DateTimeImmutable */
    protected $time;

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
        ActionInterface $parent = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->target   = $target;
        $this->quantity = $quantity;
        $this->customer = $customer;
        $this->time     = $time;
        $this->sale     = $sale;
        $this->parent   = $parent;
    }

    public function createSubaction(CustomerInterface $customer)
    {
        return new static(null, $this->type, $this->target, $this->quantity, $customer, $this->time, $this->sale, $this);
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
    public function getSale()
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

    /**
     * {@inheritdoc}
     */
    public function getParent()
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

    /**
     * @param PriceInterface $price
     * @return ChargeInterface|Charge|null
     */
    public function calculateCharge(PriceInterface $price): ?ChargeInterface
    {
        if (!$this->isApplicable($price)) {
            return null;
        }

        $usage = $price->calculateUsage($this->getQuantity());
        if ($usage === null) {
            return null;
        }

        $sum = $price->calculateSum($this->getQuantity());
        if ($sum === null) {
            return null;
        }

        /* sorry, debugging facility
         * var_dump([
            'unit'      => $usage->getUnit()->getName(),
            'quantity'  => $usage->getQuantity(),
            'price'     => $price->calculatePrice($usage)->getAmount(),
            'sum'       => $sum->getAmount(),
        ]);*/

        return new Charge(null, $this, $price, $usage, $sum);
    }
}
