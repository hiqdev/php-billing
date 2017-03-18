<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Tariff;
use hiqdev\php\billing\Type;

/**
 * Bill.
 */
class BillResource
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var Bill|null
     */
    public $type;

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
}
