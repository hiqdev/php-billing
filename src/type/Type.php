<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\type;

/**
 * General Type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Type implements TypeInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function equals(TypeInterface $other)
    {
        return $this->name === $other->getName();
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
