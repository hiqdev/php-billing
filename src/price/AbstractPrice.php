<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\SettableChargeModifierTrait;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Price.
 * @see PriceInterface
 * By default Price is applicable when same target and same type as Action.
 * But it can be different e.g. same price for all targets when certain type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractPrice implements PriceInterface, ChargeModifier
{
    use SettableChargeModifierTrait;

    /**
     * @var integer
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
     * @var PlanInterface|null
     */
    protected $plan;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        PlanInterface $plan = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->target = $target;
        $this->plan = $plan;
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
    public function getType()
    {
        return $this->type;
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
    public function getPlan(): ?PlanInterface
    {
        return $this->plan;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlan()
    {
        return $this->plan !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlan(PlanInterface $plan)
    {
        if ($this->hasPlan()) {
            throw new CannotReassignException('price plan');
        }
        $this->plan = $plan;
    }

    /**
     * {@inheritdoc}
     * Default sum calculation method: sum = price * usage.
     */
    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $usage = $this->calculateUsage($quantity);
        if ($usage === null) {
            return null;
        }

        $price = $this->calculatePrice($quantity);
        if ($price === null) {
            return null;
        }

        if ($price->isZero()) {
            return $price;
        }

        $stringQty = sprintf('%.14F', $usage->getQuantity());

        $sum = $price->multiply($stringQty, Money::ROUND_HALF_UP);
        if ($sum->isZero() && !$quantity->isZero()) {
            // If there is any usage, but sum is zero, we should charge at least 1 cent
            $sum = $price->multiply($stringQty, Money::ROUND_UP);
        }

        return $sum;
    }

    /**
     * What purpose of this method? Because it looks like duplicate of PriceHydrator::extract()
     * Where we are using the result of this method?
     * Magic calls can't be determined and I don't know what can be broken if we change the method result.
     * Which structure must have the result, because array can contain anything?
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $res = array_filter(get_object_vars($this));
        unset($res['plan']);

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(ActionInterface $action): bool
    {
        /* sorry, debugging facility
        var_dump([
            'action.target'     => $action->getTarget(),
            'price.target'      => $this->getTarget(),
            'action.type'       => $action->getType(),
            'price.type'        => $this->getType(),
            'target matches'    => $action->getTarget()->matches($this->getTarget()),
            'type matches'      => $action->getType()->matches($this->getType()),
        ]); */
        return $action->getTarget()->matches($this->getTarget()) &&
               $action->getType()->matches($this->getType());
    }
}
