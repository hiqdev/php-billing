<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

use hiqdev\php\billing\Exception\CannotReassignException;

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

    /**
     * @var string
     */
    protected $name;

    /** @var string */
    protected $label;

    public function __construct($id, $type, $name = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    public function setId($id)
    {
        if ((string) $this->id === (string) $id) {
            return;
        }
        if ($this->hasId()) {
            throw new CannotReassignException('sale id');
        }
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getFullName(): string
    {
        $type = $this->getType();
        $name = $this->getName();

        return $type === self::ANY && $name === null ? '' : "$type:$name";
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

    public function equals(TargetInterface $other): bool
    {
        return $this->getUniqueId() === $other->getUniqueId();
    }

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

    public function jsonSerialize(): array
    {
        return array_filter(array_merge(
            get_object_vars($this),
            [
                'id'    => $this->getId(),
                'type'  => $this->getType(),
                'name'  => $this->getName(),
            ],
        ));
    }

    protected static $anyTarget;

    public static function any(): self
    {
        if (static::$anyTarget === null) {
            static::$anyTarget = new static(static::ANY, static::ANY);
        }

        return static::$anyTarget;
    }
}
