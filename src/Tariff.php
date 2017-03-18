<?php

namespace hiqdev\php\billing;

use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\TariffResource;

/**
 * Tariff Plan.
 */
class Tariff
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Tariff|null
     * XXX not sure to implement
     */
    public $parent;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var Object
     */
    public $object;

    /**
     * @var TariffResource[]
     */
    public $resources = [];

}
