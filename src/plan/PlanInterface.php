<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\action\ActionInterface;

/**
 * Plan Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanInterface
{
    /**
     * Calculates charges for given action.
     * @param ActionInterface $action
     * @return ChargeInterface[]
     */
    public function calculateCharges(ActionInterface $action);
}
