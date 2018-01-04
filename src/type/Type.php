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
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function __construct($id, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
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
        return $this->id === $other->getId() &&
            $this->name === $other->getName();
    }

    public function matches(TypeInterface $other)
    {
        return $this->id === self::ANY || $other->id === self::ANY
            ? $this->checkMatches($this->name, $other->getName())
            : $this->checkMatches($this->id, $other->getId());
    }

    protected function checkMatches($lhs, $rhs)
    {
        if ($lhs === self::NONE || $rhs === self::NONE) {
            return false;
        }

        return (string) $lhs === (string) $rhs;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'name' => $this->name,
            'id' => $this->id,
        ]);
    }
}
