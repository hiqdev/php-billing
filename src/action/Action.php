<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
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
        return $this->saleOccurred() && $price->isApplicable($this);
    }

    /**
     * // TODO: think about moving to Sale::isOccurred()
     *
     * @throws \Exception
     * @return bool whether Sale that belongs to current Action occurs in current month or earlier
     */
    private function saleOccurred(): bool
    {
        if ($this->sale === null) {
            return true;
        }

        return $this->sale->getTime() < $this->getTime()->modify('first day of next month 00:00');
    }
}
