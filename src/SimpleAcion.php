<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Simple Action.
 * Charges only same target and price.
 *
 * @see ActionInterface
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
            $this->getType()->equals($price->getType());
    }
}
