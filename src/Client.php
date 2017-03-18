<?php

namespace hiqdev\php\billing;

/**
 * Client.
 */
class Client
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $login;

    /**
     * @var Client
     */
    public $seller;

    /**
     * @var Client[]
     */
    public $sellers = [];
}
