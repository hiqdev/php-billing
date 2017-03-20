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
 */
class Tariff
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Tariff|null
     * XXX not sure to implement
     */
    public $parent;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var object
     */
    public $object;

    /**
     * @var Price[]
     */
    public $prices = [];

    /**
     * Calculate charges for given action.
     * @param Action $action
     * @return Charges[]
     */
    public function calculateCharges(Action $action)
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
