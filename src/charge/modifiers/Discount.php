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

use Money\Money;

/**
 * General discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Discount extends Modifier
{
    public function fixed($value)
    {
        return new FixedDiscount($value, $this->addons);
    }

    public function grows($step, $start = null)
    {
        return new GrowingDiscount($step, $start, $this->addons);
    }

    public static function ensureValidValue($value)
    {
        if (is_numeric($value) || $value instanceof Money) {
            return $value;
        }

        /// TODO: add convertion from string

        /// TODO: add special exception
        var_dump($value);
        throw new \Exception('invalid discount value');
    }
}
