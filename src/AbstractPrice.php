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
abstract class Price implements PriceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Tariff
     */
    protected $tariff;

    /**
     * @var Target
     */
    protected $target;

    /**
     * @var Type
     */
    protected $type;

    public function __construct(TargetInterface $target, TypeInterface $type)
    {
        $this->target = $target;
        $this->type = $type;
    }

    /**
     * @inheritdoc
     * Default and only sum calculation method: sum = price * usage
     */
    public function calculateCharge(ActionInterface $action)
    {
        if (!$action->isApplicable($this->target, $this->type)) {
            return null;
        }

        $usage = $this->calculateUsage($action->getQuantity());
        if ($usage === null) {
            return null;
        }

        $price = $this->calculatePrice($usage);
        if ($price === null) {
            return null;
        }

        $sum = $price->multiply($usage->getQuantity());

        return new Charge($action, $this->target, $this->type, $usage, $sum);
    }

    /**
     * @inheritdoc
     */
    abstract public function calculateUsage(QuantityInterface $quantity);

    /**
     * @inheritdoc
     */
    abstract public function calculatePrice(QuantityInterface $usage);

    /**
     * @inheritdoc
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }
}
