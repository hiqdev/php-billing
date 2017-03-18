<?php

namespace hiqdev\php\billing;

use DateTime;

/**
 * Object being charged.
 *
 * expires, initialExpires for renewal accounting
 */
class Object
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
     * @var DateTime
     */
    public $expires;


    /**
     * @var DateTime
     */
    public $initialExpires;

    /**
     * @var integer
     */
    public $renewedNum;
}
