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
 * Billable Action Interface.
 *
 * Action knows which Prices are applicable.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ActionInterface
{
    /**
     * Returns charges calculated for this action.
     * @return ChargeInterface[]
     */
    public function getCharges();

    /**
     * Returns if the given target and type relates to this action.
     * @param TargetInterface $target
     * @param TypeInterface $type
     * @return bool
     */
    public function isApplicable(Price $price);
}
