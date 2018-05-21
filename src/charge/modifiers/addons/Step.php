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

/**
 * Discount step addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Step extends Discount
{
    protected static $name = 'step';

    public function calculateFor(int $num, ?Discount $min): Discount
    {
        $start = $min ? $min : $this;

        return $this->multiply($num)->add($start);
    }
}
