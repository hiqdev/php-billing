<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
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
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $id;

    public function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function getUniqueId()
    {
        return $this->getId() ?: $this->getName();
    }

    /**
     * @return string
     */
    public function equals(TypeInterface $other)
    {
        return $this->id === null && $other->id === null
            ? (string) $this->name === (string) $other->name
            : (string) $this->id === (string) $other->id;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'name' => $this->name,
            'id' => $this->id,
        ]);
    }
}
