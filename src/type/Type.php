<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\type;

/**
 * General Type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Type implements TypeInterface
{
    public const string MONTHLY = 'monthly';

    /**
     * @var int|string|null|float The unique identifier of the type. Can be an integer or string.
     *                            Special values:
     *                            - `Type::ANY` indicates that the type can match any ID.
     *                            - `Type::NONE` indicates that there is no valid ID.
     */
    protected $id;

    /**
     * @var string|null|float The name of the type. Can be a specific name or one of the special values:
     *                        - `Type::ANY` indicates that the type can match any name.
     *                        - `Type::NONE` indicates that there is no valid name.
     */
    protected $name;

    public function __construct($id, $name = self::ANY)
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
    public function getName(): ?string
    {
        return $this->name;
    }

    public function hasName(): bool
    {
        return !empty($this->name) && $this->name !== self::ANY && $this->name !== self::NONE;
    }

    public function getUniqueId()
    {
        return $this->hasName() ? $this->name : $this->id;
    }

    public function equals(TypeInterface $other): bool
    {
        return $this->id === $other->getId() &&
            $this->name === $other->getName();
    }

    public function matches(TypeInterface $other): bool
    {
        return $this->id === self::ANY || $other->getId() === self::ANY
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

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    public function isDefined(): bool
    {
        return $this->id !== null || $this->name !== null;
    }

    public static function anyId($name): TypeInterface
    {
        return new self(self::ANY, $name);
    }

    public function isMonthly(): bool
    {
        return $this->belongsToGroup(self::MONTHLY);
    }

    public function belongsToGroup(string $group): bool
    {
        return $this->groupName() === $group;
    }

    public function groupName(): string
    {
        $groupIndex = 0;

        return $this->extractNamePart($groupIndex);
    }

    /**
     * @param int $index - 0 - group index, 1 - local index
     * @return string
     */
    private function extractNamePart(int $index): string
    {
        $name = $this->getName();
        if (strpos($name, ',') > 0) {
            $parts = explode(',', $name, 2);
        } else {
            return $name;
        }

        return $parts[$index] ?? '';
    }

    public function belongsToLocalCategory(string $local): bool
    {
        return $this->localName() === $local;
    }

    public function localName(): string
    {
        $localIndex = 1;

        return $this->extractNamePart($localIndex);
    }
}
