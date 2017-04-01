<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Resource Type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Target implements TargetInterface
{
    /**
     * @var int|string
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    public function __construct($type, $id)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFullId()
    {
        return $this->type . ':' . $this->id;
    }
}
