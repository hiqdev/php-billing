<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use DateTime;
use hiqdev\php\units\QuantityInterface;

/**
 * Chargable Action.
 *
 * @see ActionInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var QuantityInterface
     */
    protected $quantity;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @param CustomerInterface $customer
     * @param TargetInterface $target
     * @param QuantityInterface $quantity
     * @param DateTime $time
     */
    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        QuantityInterface   $quantity,
        CustomerInterface   $customer = null,
        DateTime            $time = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->target   = $target;
        $this->quantity = $quantity;
        $this->customer = $customer;
        $this->time     = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCharge(PriceInterface $price)
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

        return new Charge(null, $this, $price->getType(), $price->getTarget(), $usage, $sum);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function isApplicable(PriceInterface $price);
}
