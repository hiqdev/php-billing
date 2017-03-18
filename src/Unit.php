<?php

namespace hiqdev\php\billing;

/**
 * Unit.
 */
class Unit
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
     * @var double
     * XXX we need some big number
     */
    public $factor;
}
