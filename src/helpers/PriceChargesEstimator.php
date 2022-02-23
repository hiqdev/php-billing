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
use Yii;

/**
 * Class PriceChargesEstimator.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */

class PriceChargesEstimator
{
    /**
     * @var \yii\i18n\Formatter
     */
    protected $yiiFormatter;
    /**
     * @var DecimalMoneyFormatter
     */
    protected $moneyFormatter;
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
        $this->yiiFormatter = Yii::$app->formatter;
        $this->moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
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

                $money = new Money($sum['amount'], new Currency($sum['currency']));
                $price = $this->moneyFormatter->format($money);

                $chargesByTargetAndAction['targets'][$targetId][$actionType]['charges'][] = [
                    'type' => $priceType,
                    'price' => $price,
                    'currency' => $sum['currency'],
                    'comment' => $charge['comment'],
                    'formattedPrice' => $this->yiiFormatter->asCurrency($price, $sum['currency']),
                ];

                $chargesByTargetAndAction['sum'] += $price;
                $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] = max(
                    $charge['action']['quantity']['quantity'],
                    $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] ?? 0
                );
                $chargesByTargetAndAction['sumFormatted'] = $this->yiiFormatter->asCurrency($chargesByTargetAndAction['sum'], $sum['currency']);
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

            $result[$this->yiiFormatter->asDate(strtotime($period), 'php:M Y')] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action)
    {
        $action['sum'] = array_sum(array_column($action['charges'], 'price'));
        $action['currency'] = reset($action['charges'])['currency'];
        $action['sumFormatted'] = $this->yiiFormatter->asCurrency($action['sum'], $action['currency']);
    }
}
