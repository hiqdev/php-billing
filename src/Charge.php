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
        MoneyInterface      $sum
    ) {
        $this->action   = $action;
        $this->target   = $target;
        $this->type     = $type;
        $this->usage    = $usage;
        $this->sum      = $sum;
    }
}
