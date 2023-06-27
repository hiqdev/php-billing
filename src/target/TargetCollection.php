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

use hiqdev\php\billing\Exception\UnknownEntityException;

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

    protected array $states = [];

    public function __construct(array $targets)
    {
        $ids = [];
        $types = [];
        $states = [];
        foreach ($targets as $target) {
            if ($target instanceof TargetInterface) {
                $ids[] = $target->getId();
                $types[] = $target->getType();
                $states[] = $target->getState();
                $this->targets[] = $target;
            } else {
                throw new UnknownEntityException('The target is invalid');
            }
        }
        $this->ids = array_unique(array_filter($ids));
        $this->types = array_unique(array_filter($types));
        $this->states = array_unique(array_filter($states));
    }

    public function add(TargetInterface $target)
    {
        $this->targets[] = $target;
        $this->ids[] = $target->getId();
        $this->types[] = $target->getType();
        $this->states[] = $target->getState();
        $this->ids = array_unique(array_filter($this->ids));
        $this->types = array_unique(array_filter($this->types));
        $this->states = array_unique(array_filter($this->states));
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

    public function getState(): ?string
    {
        return $this->getTarget()?->getState();
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getStates(): array
    {
        return $this->states;
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

    public static function takeStates(TargetInterface $other): array
    {
        return $other instanceof static ? $other->states : [$other->getState()];
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
