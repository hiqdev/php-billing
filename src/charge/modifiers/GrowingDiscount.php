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

use DateInterval;
use Money\Money;

/**
 * Growing discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class GrowingDiscount extends FixedDiscount
{
    /**
     * @var int|Money
     */
    protected $step;

    /**
     * @var int|Money
     */
    protected $min;

    /**
     * @var int|Money
     */
    protected $max;

    /**
     * @var DateInterval
     */
    protected $period;

    public function __construct($step, $min = null, array $addons = [])
    {
        Modifier::__construct($addons);
        $this->step = static::ensureValidValue($step);
        if ($min) {
            $this->min($min);
        }
    }

    public function isAbsolute()
    {
        return $this->step instanceof Money;
    }

    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    public function min($min)
    {
        $this->min = $this->ensureValidLimit($min, 'min');

        return $this;
    }

    public function max($max)
    {
        $this->max = $this->ensureValidLimit($max, 'max');

        return $this;
    }

    public function ensureValidLimit($limit, $name)
    {
        $limit = static::ensureValidValue($limit);
        if ($limit instanceof Money) {
            if ($this->isRelative()) {
                throw new \Exception("$name must be given as percentage because step is percentage");
            }
        } elseif ($this->isAbsolute()) {
            throw new \Exception("$name must be money because step is money");
        }

        return $limit;
    }

    public function everyMonth($num = 1)
    {
        if ($this->period !== null) {
            throw new \Exception('periodicity is already set');
        }
        if (empty($num)) {
            $num = 1;
        }
        if (filter_var($num, FILTER_VALIDATE_INT) === false) {
            throw new \Exception('periodicity must be integer number');
        }
        $this->period = new DateInterval("P${num}M");

        return $this;
    }
}
