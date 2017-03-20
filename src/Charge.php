<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Tariff;
use hiqdev\php\billing\Type;

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
