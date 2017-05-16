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
 * Tariff Plan.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Tariff implements TariffInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Tariff|null
     * XXX not sure to implement
     */
    protected $parent;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Target
     */
    protected $target;

    /**
     * @var PriceInterface[]
     */
    protected $prices = [];

    /**
     * @param PriceInterface[] $prices
     */
    public function __construct(array $prices)
    {
        $this->prices = $prices;
    }

    /**
     * @return PriceInterface[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Calculate charges for given action.
     * @param ActionInterface $action
     * @return Charge[]
     */
    public function calculateCharges(ActionInterface $action)
    {
        $charges = [];
        foreach ($this->prices as $price) {
            $charge = $price->calculateCharge($action);
            if ($charge !== null) {
                $charges[] = $charge;
            }
        }

        return $charges;
    }
}
