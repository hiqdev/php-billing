<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * Fixed discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FixedDiscount extends Discount
{
    /**
     * @var int|Money
     */
    protected $value;

    public function __construct($value, array $addons = [])
    {
        parent::__construct($addons);
        $this->value = static::ensureValidValue($value);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isAbsolute()
    {
        return $this->value instanceof Money;
    }

    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    public function calculateSum(Money $sum)
    {
        return $this->isAbsolute() ? $this->value : $sum->multiply($this->value*0.01);
    }

    public function buildPrice(Money $sum)
    {
        $type = $this->getType();
        $target = $this->getTarget();
        $prepaid = Quantity::items(0);

        return new SinglePrice(null, $type, $target, null, $prepaid, $sum);
    }

    public function getType()
    {
        return new Type(Type::ANY, 'discount');
    }

    public function getTarget()
    {
        return new Target(Target::ANY, Target::ANY);
    }

    public function modifyCharge(ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            return [];
        }
        $sum = $this->calculateSum($charge->getSum());
        $usage  = Quantity::items(1);
        $price = $this->buildPrice($sum);
        $discount = new Charge(null, $action, $price, $usage, $sum);

        return [$charge, $discount];
    }
}
