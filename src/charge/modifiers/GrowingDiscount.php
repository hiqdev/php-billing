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
 * Growing discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class GrowingDiscount extends Discount
{
    /**
     * @var int|Money
     */
    protected $step;

    /**
     * @var int|Money
     */
    protected $start;

    public function __construct($step, $start = null, array $addons = [])
    {
        parent::__construct($addons);
        $this->step = $step;
        $this->start = $start;
    }
}
