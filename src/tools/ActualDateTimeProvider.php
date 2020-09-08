<?php
declare(strict_types=1);

namespace hiqdev\php\billing\tools;

use DateTimeImmutable;

final class ActualDateTimeProvider implements CurrentDateTimeProviderInterface
{
    public function dateTimeImmutable(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
