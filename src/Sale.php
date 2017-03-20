<?php

namespace hiqdev\php\billing;

use DateTime;

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

    public function calculateChar
}
