<?php declare(strict_types=1);

namespace hiqdev\php\billing\customer;

enum CustomerState: string
{
    case DELETED = 'deleted';

    public static function isDeleted(CustomerInterface $customer): bool
    {
        return self::tryFrom((string)$customer->getState()) === self::DELETED;
    }
}
