<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\price\PriceInterface;
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
    /** @var int */
    protected $id;

    /** @var ActionInterface */
    protected $action;

    /** @var PriceInterface */
    protected $price;

    /** @var QuantityInterface */
    protected $usage;

    /** @var Money */
    protected $sum;

    /** @var BillInterface */
    protected $bill;

    /** @var ChargeState */
    protected $state;

    /** @var string */
    protected $comment;

    public function __construct(
                            $id,
        ActionInterface     $action,
        PriceInterface      $price,
        QuantityInterface   $usage,
        Money               $sum,
        BillInterface       $bill = null
    ) {
        $this->id       = $id;
        $this->action   = $action;
        $this->price    = $price;
        $this->usage    = $usage;
        $this->sum      = $sum;
        $this->bill     = $bill;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAction()
    {
        return $this->action;
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

    public function getBill()
    {
        return $this->bill;
    }

    public function hasBill()
    {
        return $this->bill !== null;
    }

    public function setBill(BillInterface $bill)
    {
        if ($this->hasBill()) {
            throw new \Exception('cannot reassign sale bill');
        }
        $this->bill = $bill;
    }

    public function getState(): ?ChargeState
    {
        return $this->state;
    }

    public function setFinished(): void
    {
        $this->state = ChargeState::finished();
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
