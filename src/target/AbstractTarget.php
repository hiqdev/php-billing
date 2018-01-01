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
        return $this->type . ':' . $this->id;
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
        if ($this->id === null) {
            if ($this->type === null) {
                return true;
            }

            return (string) $this->type === (string) $other->getType();
        }

        if ($this->type === null) {
            return (string) $this->id === (string) $other->id;
        }

        return $this->equals($other);
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
