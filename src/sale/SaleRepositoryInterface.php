<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface SaleRepositoryInterface
{
    /**
     * Finds sale for given action: customer + type + target.
     * @param ActionInterface $action
     * @return PlanInterface
     */
//  public function findByAction(ActionInterface $action);

    /**
     * Finds suitable sales for given order: customer + actions.
     * @param ActionInterface $action
     * @return PlanInterface[] array: actionKey => plan
     */
//  public function findByOrder(OrderInterface $order);

    /**
     * Finds sale by given ids.
     * @param int[] $ids
     * @return PlanInterface[] array
     */
//  public function findByIds(array $ids);
}
