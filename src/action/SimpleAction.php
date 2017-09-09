<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

use hiqdev\php\billing\price\PriceInterface;

/**
 * Simple Action.
 * Charges only same target and same type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SimpleAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function isApplicable(PriceInterface $price)
    {
        return $this->target->equals($price->getTarget()) &&
            $this->type->equals($price->getType());
    }
}
