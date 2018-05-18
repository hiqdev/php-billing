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

use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Discount step addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Step extends Discount
{
    public function calculateFor(int $num, ?Discount $min, ?Discount $max)
    {
        return $this->getValue();

        $min = $min ? $min->getValue() : $this->getStep()->getValue();
        $dif = $this->getStep()->multiply($num);
    }
}
