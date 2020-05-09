<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use hiqdev\php\billing\charge\ChargeInterface;
use Money\Money;

/**
 * Discount maximum addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Maximum extends Extremum
{
    protected static $name = 'maximum';

    public function calculateSum(ChargeInterface $charge): Money
    {
        return $this->value instanceof Money
            ? $this->value
            : $charge->getSum()->multiply($this->value*0.01);
    }
}
