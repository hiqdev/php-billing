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
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
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
     */
    public function calculateCharge(ActionInterface $action)
    {
        if (!$action->isApplicable($this)) {
            return null;
        }

        $usage = $this->calculateUsage($action);
        if ($usage === null) {
            return null;
        }

        $sum = $this->calculateSum($action);
        if ($sum === null) {
            return null;
        }

        return new Charge($action, $this->target, $this->type, $usage, $sum);
    }

    /**
     * @inheritdoc
     * Default sum calculation method: sum = price * usage
     */
    public function calculateSum(ActionInterface $action)
    {
        $usage = $this->calculateUsage($action);
        if ($usage === null) {
            return null;
        }

        $price = $this->calculatePrice($action);
        if ($price === null) {
            return null;
        }

        $sum = $price->multiply($usage->getQuantity());
    }

    /**
     * @inheritdoc
     */
    abstract public function calculateUsage(ActionInterface $action);

    /**
     * @inheritdoc
     */
    abstract public function calculatePrice(ActionInterface $action);

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
