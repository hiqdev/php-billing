<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Type;
use hiqdev\php\billing\Sale;
use hiqdev\php\billing\Object;

/**
 * Billable Action.
 */
class Action
{
    public $id;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var Sale
     */
    public $sale;

    /**
     * @var DateTime
     */
    public $time;

    /**
     * @var Object
     */
    public $object;

    public $amount;

}
