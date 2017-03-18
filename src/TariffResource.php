<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Tariff;
use hiqdev\php\billing\Type;
use hiqdev\php\billing\Unit;

/**
 * Tariff Resource.
 */
class TariffResource
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
     * @var Tariff
     */
    public $tariff;

    /**
     * @var Object
     */
    public $object;

    /**
     * @var Unit
     */
    public $unit;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var integer
     */
    public $price;
}
