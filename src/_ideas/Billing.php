<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\order;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Billing
{
    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function calculateBills(OrderInterface $order)
    {
        $charges = $this->calculateCharges($order);

        return $this->aggregateCharges($charges);
    }

    public function calculateCharges(OrderInterface $order)
    {
        $planRepo = $this->entityManager->getRepository(Plan::class);
        // TODO should be like this:
        // $spec = Specification::fromArray(['order' => $order]);
        // $plans = $planRepo->findAll($spec);
        $plans = $planRepo->findByOrder($order);
        $charges = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (empty($plans[$actionKey])) {
                throw new FailedFindPlan();
            }
            $charges[$actionKey] = $plans[$actionKey]->calculateCharges($action);
        }

        return $charges;
    }

    public function aggregateCharges(array $charges)
    {
        var_dump(__METHOD__);
        var_dump($charges);

        die(__METHOD__);
    }
}
