<?php

namespace hiqdev\php\billing;

/**
 * Client.
 */
class Purse
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var integer
     */
    public $credit;
}
