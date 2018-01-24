<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
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
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    public function __construct($id, $type)
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        $id = $this->getId();
        $type = $this->getType();

        return ($type === self::ANY ? '' : $type) . ':' . ($id === self::ANY ? '' : $id);
    }

    /**
     * @return bool
     */
    public function equals(TargetInterface $other): bool
    {
        return $this->getUniqueId() === $other->getUniqueId();
    }

    /**
     * @return bool
     */
    public function matches(TargetInterface $other): bool
    {
        return $this->checkMatches($other) || $other->checkMatches($this);
    }

    public function checkMatches(TargetInterface $other): bool
    {
        if ($this->id === self::ANY) {
            if ($this->type === self::ANY) {
                return true;
            }

            return $this->matchTypes($other);
        }

        if ($this->type === self::ANY) {
            return $this->matchIds($other);
        }

        return $this->matchIds($other) && $this->matchTypes($other);
    }

    protected function matchIds(TargetInterface $other)
    {
        return $this->matchStrings($this->id, $other->getId());
    }

    protected function matchTypes(TargetInterface $other)
    {
        return $this->matchStrings($this->type, $other->getType());
    }

    protected function matchStrings($lhs, $rhs)
    {
        if ($lhs === self::NONE || $rhs === self::NONE) {
            return false;
        }

        return (string) $lhs === (string) $rhs;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
