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
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $uniqId;

    public function __construct($type, $id)
    {
        $this->id = $id;
        $this->type = $type;
        $this->uniqId = $type . ':' . $id;
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
    public function getUniqId()
    {
        return $this->uniqId;
    }

    /**
     * @return bool
     */
    public function equals(TargetInterface $other)
    {
        return $this->uniqId === $other->getUniqId();
    }
}
