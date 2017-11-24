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

use DateTime;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
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
     * @var SaleInterface
     */
    protected $sale;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @param TypeInterface $type
     * @param TargetInterface $target
     * @param QuantityInterface $quantity
     * @param CustomerInterface $customer
     * @param SaleInterface $sale
     * @param DateTime $time
     */
    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        QuantityInterface   $quantity,
        CustomerInterface   $customer = null,
        SaleInterface       $sale = null,
        DateTime            $time = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->target   = $target;
        $this->quantity = $quantity;
        $this->customer = $customer;
        $this->sale     = $sale;
        $this->time     = $time;
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
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->time;
    }

    public function setId($id)
    {
        if ($this->id === $id) {
            return;
        }
        if ($this->id !== null) {
            throw new \Exception('cannot reassign action id');
        }
        $this->id = $id;
    }

    public function setSale(SaleInterface $sale)
    {
        if ($this->sale !== null) {
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

        return new Charge(null, $this, $price, $this->getTarget(), $usage, $sum);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function isApplicable(PriceInterface $price);
}
