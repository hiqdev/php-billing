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
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanRepositoryInterface
{
    /**
     * Finds suitable plan for given action: customer + type + target.
     * @param ActionInterface $action
     * @return PlanInterface
     */
    public function findByAction(ActionInterface $action);

    /**
     * Finds suitable plans for given order: customer + actions
     * @param ActionInterface $action
     * @return PlanInterface[] array: actionKey => plan
     */
    public function findByOrder(OrderInterface $order);
}
