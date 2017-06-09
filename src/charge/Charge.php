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
        ActionInterface     $action,
        TypeInterface       $type,
        TargetInterface     $target,
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

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}