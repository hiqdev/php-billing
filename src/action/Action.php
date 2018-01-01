<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use hiqdev\php\billing\price\PriceInterface;

/**
 * Simple Action.
 * It is applicable when price is applicable.
 * But it can be different see Coupon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Action extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function isApplicable(PriceInterface $price): bool
    {
        return $price->isApplicable($this);
    }
}
