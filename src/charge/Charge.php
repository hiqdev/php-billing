<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Charge.
 *
 * [[Action]] is charged with a number of [[Charge]]s.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Charge implements ChargeInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var QuantityInterface
     */
    protected $usage;

    /**
     * @var Money
     */
    protected $sum;

    /**
     * @var DateTimeImmutable
     */
    protected $time;

    public function __construct(
                            $id,
        ActionInterface     $action = null,
        PriceInterface      $price = null,
        TargetInterface     $target = null,
        QuantityInterface   $usage,
        Money               $sum,
        DateTimeImmutable   $time = null
    ) {
        $this->id       = $id;
        $this->action   = $action;
        $this->price    = $price;
        $this->target   = $target;
        $this->usage    = $usage;
        $this->sum      = $sum;
        $this->time     = $time;
    }

    /**
     * Returns charge that is sum of given charges.
     * @param Charge[] $charges
     * @return Charge
     */
    public static function sumUp(array $charges)
    {
        if (empty($charges)) {
            return new self(null, null, null, null, Quantity::item(0), Money::USD(0));
        }

        $first = array_unshift($charges);
        if (empty($charges)) {
            return $first;
        }

        throw new \Exception('Not implemented Charge::sumUp');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return PriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function getUsage()
    {
        return $this->usage;
    }

    public function getSum()
    {
        return $this->sum;
    }

    public function calculatePrice()
    {
        $usage = $this->usage->getQuantity();

        return $usage ? $this->sum->divide($usage) : $this->sum;
    }

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
            throw new \Exception('cannot reassign sale id');
        }
        $this->id = $id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
