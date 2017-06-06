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

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Charge.
 *
 * [[Action]] is charged with a number of [[Charge]]s.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Charge
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var ActionInterface
     */
    public $action;

    /**
     * @var TargetInterface
     */
    public $target;

    /**
     * @var TypeInterface
     */
    public $type;

    /**
     * @var QuantityInterface
     */
    public $usage;

    /**
     * @var Money
     */
    public $sum;

    public function __construct(
        ActionInterface     $action,
        TargetInterface     $target,
        TypeInterface       $type,
        QuantityInterface   $usage,
        Money               $sum
    ) {
        $this->action   = $action;
        $this->target   = $target;
        $this->type     = $type;
        $this->usage    = $usage;
        $this->sum      = $sum;
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
}
