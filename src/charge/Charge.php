<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Charge.
 *
 * [[Action]] is charged with a number of [[Charge]]s.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Charge implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var QuantityInterface
     */
    protected $usage;

    /**
     * @var Money
     */
    protected $sum;

    public function __construct(
                            $id,
        ActionInterface     $action = null,
        TypeInterface       $type = null,
        TargetInterface     $target = null,
        QuantityInterface   $usage,
        Money               $sum
    ) {
        $this->id       = $id;
        $this->action   = $action;
        $this->type     = $type;
        $this->target   = $target;
        $this->usage    = $usage;
        $this->sum      = $sum;
    }

    /**
     * Returns charge that is sum of given charges.
     * @param Charge[] $charges
     * @return Charge
     */
    public static function sumUp(array $charges)
    {
        if (empty($charges)) {
            return new Charge(null, null, null, null, Quantity::item(0), Money::USD(0));
        }

        $first = array_unshift($charges);
        if (empty($charges)) {
            return $first;
        }

        throw new \Exception('Not implemented Charge::sumUp');
    }
    public function getId()
    {
        return $this->id;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUsage()
    {
        return $this->usage;
    }

    public function getSum()
    {
        return $this->sum;
    }

    public function getPrice()
    {
        $usage = $this->usage->getQuantity();

        return $usage ? $this->sum->divide($usage) : $this->sum ;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
