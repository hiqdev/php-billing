<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\price\PriceInterface;

/**
 * Tariff Plan.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Plan implements PlanInterface
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
     * @var Plan|null
     * XXX not sure to implement
     */
    protected $parent;

    /**
     * @var CustomerInterface
     */
    protected $seller;

    /**
     * @var PriceInterface[]
     */
    protected $prices = [];

    /**
     * @param PriceInterface[] $prices
     */
    public function __construct(
                            $id,
                            $name,
        CustomerInterface   $seller = null,
        array               $prices = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->seller = $seller;
        $this->prices = $prices;
    }

    public function getUniqueId()
    {
        return $this->getId();
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PriceInterface[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param PriceInterface[] $prices
     */
    public function setPrices(array $prices)
    {
        if ($this->prices !== []) {
            throw new \Exception('cannot reassign prices for plan');
        }
        $this->prices = $prices;
    }

    /**
     * Calculate charges for given action.
     * @param ActionInterface $action
     * @return Charge[]|ChargeInterface[]
     */
    public function calculateCharges(ActionInterface $action)
    {
        $charges = [];
        foreach ($this->prices as $price) {
            $charge = $action->calculateCharge($price);
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
