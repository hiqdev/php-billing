<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

/**
 * @see TargetInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractTarget implements TargetInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var int|string
     */
    protected $id;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
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
        return $this->type . ':' . $this->id;
    }

    /**
     * @return bool
     */
    public function equals(TargetInterface $other)
    {
        return $this->getUniqId() === $other->getUniqId();
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
