<?php declare(strict_types=1);

namespace hiqdev\php\billing\customer;

use hiqdev\php\billing\Exception\CustomerStateException;

class CustomerState
{
    public const BLOCKED = 'blocked';

    public const DELETED = 'deleted';

    public const NEW = 'new';

    public const OK = 'ok';

    private function __construct(protected string $state = self::NEW)
    {
    }

    public function getName(): string
    {
        return $this->state;
    }

    public static function isDeleted(CustomerInterface $customer): bool
    {
        return $customer->getState()?->getName() === self::DELETED;
    }

    public static function deleted(): CustomerState
    {
        return new self(self::DELETED);
    }

    public static function blocked(): CustomerState
    {
        return new self(self::BLOCKED);
    }

    public static function new(): CustomerState
    {
        return new self(self::NEW);
    }

    public static function ok(): CustomerState
    {
        return new self(self::OK);
    }

    public static function fromString(string $name): self
    {
        $allowedStates = [
            self::BLOCKED,
            self::DELETED,
            self::NEW,
            self::OK,
        ];
        foreach ($allowedStates as $state) {
            if ($state === $name) {
                return new self($state);
            }
        }

        throw new CustomerStateException("wrong customer state '$name'");
    }
}
