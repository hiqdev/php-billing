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
 */
class Charge
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var Action
     */
    public $action;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var int
     */
    public $sum;

    /**
     * @var Bill|null
     */
    public $bill;

    public function __construct(Action $action, double $quantity, int $sum)
    {
        $this->action = $action;
        $this->quantity = $quantity;
        $this->sum = $sum;
    }
}
