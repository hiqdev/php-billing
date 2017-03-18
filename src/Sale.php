<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Tariff;

/**
 * Sale.
 */
class Sale
{
    public $id;

    /**
     * @var Object
     */
    public $object;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var Tariff
     */
    public $tariff;

    /**
     * @var DateTime
     */
    public $time;
}
