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
 * Price.
 * @see PriceInterface
 */
class Price implements PriceInterface
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
     * @var Target
     */
    protected $target;

    /**
     * @var Quantity prepaid quantity also implies Unit
     */
    protected $prepaid;

    /**
     * @var Money
     */
    protected $price;

    public function __construct(
        TariffInterface     $tariff,
        TargetInterface     $target,
        TypeInterface       $type,
        MoneyInterface      $price,
        QuantityInterface   $prepaid
    ) {
        $this->tariff   = $tariff;
        $this->target   = $target;
        $this->type     = $type;
        $this->price    = $price;
        $this->prepaid  = $prepaid;
    }

    /**
     * Calculate action value.
     * @param ActionInterface $action
     * @return null|ChargeInterface
     */
    public function calculateCharge(ActionInterface $action)
    {
        if (!$action->isApplicable($this->target, $this->type)) {
            return null;
        }

        $usage = $this->calculateUsage($action->getQuantity());
        $price = $this->calculatePrice($usage);

        return Charge($action, $this->target, $this->type, $usage, $price->multiply($usage));
    }
}
