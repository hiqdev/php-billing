<?php

declare(strict_types=1);

namespace hiqdev\php\billing\target;

enum TargetState: string
{
    case DELETED = 'deleted';
    case DISABLED = 'disabled';
    case OK = 'ok';

    public static function isDeleted(TargetInterface $target): bool
    {
        return self::tryFrom((string)$target->getState()) === self::DELETED;
    }
}
