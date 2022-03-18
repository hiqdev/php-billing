<?php

/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\helpers;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * Class PriceChargesEstimator.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */

class PriceChargesEstimator
{
    /**
     * @var array
     */
    protected $calculations = [];

    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     */
    private $periods = [];

    public function __construct(array $calculations)
    {
        $this->calculations = $calculations;
    }

    public function __invoke($periods)
    {
        $this->calculateForPeriods($periods);
    }

    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     * @return array
     */
    public function calculateForPeriods($periods): array
    {
        $this->periods = $periods;

        return $this->groupCalculationsByTarget();
    }

    private function groupCalculationsByTarget()
    {
        $result = [];

        foreach ($this->calculations as $period => $charges) {
            $chargesByTargetAndAction = [];

            foreach ($charges as $charge) {
                $action = $charge['action'];

                $targetId = $action['target']['id'];
                $actionType = $action['type']['name'];
                $priceType = $charge['price']['type']['name'];
                $sum = $charge['sum'];

                $chargesByTargetAndAction['targets'][$targetId][$actionType]['charges'][] = [
                    'sum' => $sum['amount'],
                    'type' => $priceType,
                    'currency' => $sum['currency'],
                    'comment' => $charge['comment'],
                ];

                $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] = max(
                    $charge['action']['quantity']['quantity'],
                    $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] ?? 0
                );

                $chargesByTargetAndAction['currency'] = $sum['currency'];
            }
            unset($action);

            if (!empty($chargesByTargetAndAction['targets'])) {
                foreach ($chargesByTargetAndAction['targets'] as &$actions) {
                    foreach ($actions as &$action) {
                        $this->decorateAction($action);
                    }
                }
            }
            unset($action, $actions);

            $result[date("Y-m-d", strtotime($period))] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action)
    {
        $action['currency'] = reset($action['charges'])['currency'];
    }
}
