<?php

namespace hiqdev\php\billing;

use DateTime;

/**
 * Price.
 * Tariff consists of prices.
 */
class Price
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var Tariff
     */
    protected $tariff;

    /**
     * @var Object
     */
    protected $object;

    /**
     * @var Unit
     */
    protected $unit;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var double
     */
    protected $quantity;

    /**
     * @var integer
     */
    protected $price;

    /**
     * Calculate action value.
     * @param Action $action
     * @return null|BillResource
     */
    public function calculateCharge(Action $action)
    {
        if (!$this->isApplicable($action)) {
            return null;
        }

        $quantity = 1;
        $sum = 1;

        return Charge($action, $quantity, $sum);
    }

}
