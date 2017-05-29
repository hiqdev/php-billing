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
class Plan implements EntityInterface
{
    //  public static function instantiate($row)
    //  {
    //      return new static;
    //  }

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Plan|null
     * XXX not sure to implement
     */
    protected $parent;

    /**
     * @var CustomerInterface
     */
    protected $seller;

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
    public function __construct($id, $name, CustomerInterface $seller, array $prices = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->seller = $seller;
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

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
