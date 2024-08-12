<?php declare(strict_types=1);

namespace hiqdev\php\billing\customer;

enum CustomerState: string
{
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case NEW = 'new';
    case OK = 'ok';

    public static function isDeleted(CustomerInterface $customer): bool
    {
        return $customer->getState() === self::DELETED;
    }

    public static function deleted(): CustomerState
    {
        return self::DELETED;
    }
}
