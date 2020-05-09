<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

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

    public function grows($step, $min = null)
    {
        return new GrowingDiscount($step, $min, $this->addons);
    }
}
