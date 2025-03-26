<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\Exception\EntityNotFoundException;
use hiqdev\php\billing\order\OrderInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanRepositoryInterface
{
    /**
     * Finds suitable plan for given action: customer + type + target.
     * @return PlanInterface
     */
    public function findByAction(ActionInterface $action);

    /**
     * Finds suitable plans for given order: customer + actions.
     * @return PlanInterface[] array: actionKey => plan
     */
    public function findByOrder(OrderInterface $order);

    /**
     * Finds plans by given ids.
     * @param int[] $ids
     * @return PlanInterface[] array
     */
    public function findByIds(array $ids);

    /**
     * @throws EntityNotFoundException
     */
    public function getById(int $id): PlanInterface;

    public function save(PlanInterface $action): void;
}
