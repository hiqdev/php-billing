<?php

namespace hiqdev\php\billing;

use DateTime;

/**
 * Bill.
 */
class Bill
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var Purse
     */
    public $purse;

    /**
     * @var Object
     */
    public $object;

    /**
     * @var Tariff
     */
    public $tariff;

    /**
     * @var DateTime
     */
    public $time;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var integer
     */
    public $sum;

    /**
     * @var bool
     */
    public $isFinished;

    /**
     * @var Resource[]
     */
    public $resources = [];
}
