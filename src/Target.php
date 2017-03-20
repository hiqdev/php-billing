<?php

namespace hiqdev\php\billing;

use DateTime;

/**
 * Target - object being charged: domain, server
 * Holds: expires, initialExpires for renewal accounting
 */
class Target
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var DateTime
     */
    protected $expires;


    /**
     * @var DateTime
     */
    protected $initialExpires;

    /**
     * @var integer
     */
    protected $renewedNum;
}
