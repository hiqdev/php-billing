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

/**
 * @see TargetInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TargetCollection implements TargetInterface
{
    /**
     * @var TargetInterface[]
     */
    protected $targets;

    protected $ids;

    protected $types;

    public function __construct(array $targets)
    {
        $this->targets = $targets;
        $ids = [];
        $types = [];
        foreach ($targets as $target) {
            if ($target) {
                $ids[] = $target->getId();
                $types[] = $target->getType();
            }
        }
        $this->ids = array_unique(array_filter($ids));
        $this->types = array_unique(array_filter($types));
    }

    public function add(TargetInterface $target)
    {
        $this->targets[] = $target;
        $this->ids[] = $target->getId();
        $this->types[] = $target->getType();
        $this->ids = array_unique(array_filter($this->ids));
        $this->types = array_unique(array_filter($this->types));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getTarget()?->getId();
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->getId() !== null;
    }

    public function getIds()
    {
        return $this->ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getTarget()?->getType();
    }

    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getTarget()?->getName();
    }

    public function getLabel(): ?string
    {
        return $this->getTarget()?->getLabel();
    }

    public function getTarget()
    {
        return reset($this->targets);
    }

    public function getTargets()
    {
        return $this->targets;
    }

    public function getFullName(): string
    {
        return $this->getTarget()?->getFullName();
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->getTarget()?->getUniqueId();
    }

    public function equals(TargetInterface $other): bool
    {
        return $this->getUniqueId() === $other->getUniqueId();
    }

    public function matches(TargetInterface $other): bool
    {
        return $this->checkMatches($other);
    }

    public function checkMatches(TargetInterface $other): bool
    {
        foreach ($this->targets as $target) {
            if ($target->checkMatches($other) || $other->checkMatches($target)) {
                return true;
            }
        }

        return false;
    }

    public static function takeIds(TargetInterface $other)
    {
        return $other instanceof static ? $other->ids : [$other->getId()];
    }

    public static function takeTypes(TargetInterface $other)
    {
        return $other instanceof static ? $other->types : [$other->getType()];
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
