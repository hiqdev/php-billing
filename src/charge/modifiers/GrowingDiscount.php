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

use DateTimeImmutable;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\addons\Discount;
use hiqdev\php\billing\charge\modifiers\addons\Maximum;
use hiqdev\php\billing\charge\modifiers\addons\Minimum;
use hiqdev\php\billing\charge\modifiers\addons\Period;
use hiqdev\php\billing\charge\modifiers\addons\Step;
use Money\Money;

/**
 * Growing discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class GrowingDiscount extends FixedDiscount
{
    const PERIOD = 'period';
    const STEP = 'step';
    const MIN = 'min';
    const MAX = 'max';

    public function __construct($step, $min = null, array $addons = [])
    {
        Modifier::__construct($addons);
        $this->addAddon(self::STEP, new Step($step));
        if ($min) {
            $this->min($min);
        }
    }

    public function isAbsolute()
    {
        return $this->getStep()->isAbsolute();
    }

    public function getStep()
    {
        return $this->getAddon(self::STEP);
    }

    public function getMin()
    {
        return $this->getAddon(self::MIN);
    }

    public function getMax()
    {
        return $this->getAddon(self::MAX);
    }

    public function min($min)
    {
        $min = new Minimum($min);
        $this->getStep()->ensureSameType($min, 'minimum');

        return $this->addAddon(self::MIN, $min);
    }

    public function max($max)
    {
        return $this->addAddon(self::MAX, new Maximum($max));
    }

    public function getPeriod()
    {
        return $this->getAddon(self::PERIOD);
    }

    public function every($string = 1)
    {
        return $this->addAddon(self::PERIOD, Period::fromString($string));
    }

    public function calculateSum(ChargeInterface $charge = null): Money
    {
        $sum = parent::calculateSum($charge);

        if ($this->getMax() !== null) {
            $max = $this->getMax()->calculateSum($charge);
            if ($sum->compare($max) > 0) {
                $sum = $max;
            }
        }

        return $sum;
    }

    public function getValue(ChargeInterface $charge = null): Discount
    {
        if ($this->getMax() === null && $this->getTill() === null) {
            throw new \Exception("growing discount must be limited with 'max' or 'till'");
        }
        $time = $charge ? $charge->getAction()->getTime() : new DateTimeImmutable();
        $num = (int) $this->countPeriodsPassed($time);

        return $this->getStep()->calculateFor($num, $this->getMin());
    }

    protected function countPeriodsPassed(DateTimeImmutable $time)
    {
        $since = $this->getSince();
        if ($since === null) {
            throw new \Exception('no since given for growing discount');
        }

        $period = $this->getPeriod();
        if ($period === null) {
            throw new \Exception('no period given for growing discount');
        }

        return $period->countPeriodsPassed($since->getValue(), $time);
    }
}
