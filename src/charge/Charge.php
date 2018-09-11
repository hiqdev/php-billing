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
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\billing\target\TargetInterface;
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

    /** @var TypeInterface */
    protected $type;

    /** @var TargetInterface */
    protected $target;

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

    /** @var ChargeInterface|null */
    protected $parent;

    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        ActionInterface     $action,
        PriceInterface      $price,
        QuantityInterface   $usage,
        Money               $sum,
        BillInterface       $bill = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->target   = $target;
        $this->action   = $action;
        $this->price    = $price;
        $this->usage    = $usage;
        $this->sum      = $sum;
        $this->bill     = $bill;
    }

    /**
     * Provides unique string.
     * Can be used to compare or aggregate charges.
     */
    public function getUniqueString(): string
    {
        $parts = [
            'currency'  => $this->sum->getCurrency()->getCode(),
            'action'    => $this->action->getUniqueString(),
        ];

        return implode('-', $parts);
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function getTarget(): TargetInterface
    {
        return $this->target;
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * @return PriceInterface
     */
    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function getUsage(): QuantityInterface
    {
        return $this->usage;
    }

    public function getSum(): Money
    {
        return $this->sum;
    }

    public function calculatePrice(): Money
    {
        $usage = $this->usage->getQuantity();

        return $usage ? $this->sum->divide($usage) : $this->sum;
    }

    public function getBill(): ?BillInterface
    {
        return $this->bill;
    }

    public function hasBill()
    {
        return $this->bill !== null;
    }

    public function setBill(BillInterface $bill): ChargeInterface
    {
        /*if ($this->hasBill()) {
            throw new \Exception('cannot reassign sale bill');
        }*/
        $this->bill = $bill;

        return $this;
    }

    public function getState(): ?ChargeState
    {
        return $this->state;
    }

    public function setFinished(): ChargeInterface
    {
        $this->state = ChargeState::finished();

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->state === null ? null : $this->state->isFinished();
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(string $comment): ChargeInterface
    {
        $this->comment = $comment;

        return $this;
    }

    public function setId($id): ChargeInterface
    {
        if ((string)$this->id === (string)$id) {
            return $this;
        }
        if ($this->id !== null) {
            throw new \Exception('cannot reassign charge id');
        }
        $this->id = $id;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return ChargeInterface|null
     */
    public function getParent(): ?ChargeInterface
    {
        return $this->parent;
    }

    /**
     * @param ChargeInterface|null $parent
     *
     * @return Charge
     * @throws \Exception if parent is already set
     */
    public function setParent(ChargeInterface $parent): self
    {
        if ($this->parent !== null) {
            throw new \Exception('cannot reassign charge parent');
        }

        $this->parent = $parent;

        return $this;
    }
}
