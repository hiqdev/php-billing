<?php
declare(strict_types=1);

namespace hiqdev\php\billing\tools;

use DateTimeImmutable;

/**
 * Class CachedDateTimeProvider stores the dateTime on the class creation
 * and always returns it.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class CachedDateTimeProvider implements CurrentDateTimeProviderInterface
{
    private DateTimeImmutable $dateTimeImmutable;

    public function __construct(DateTimeImmutable $dateTimeImmutable)
    {
        $this->dateTimeImmutable = $dateTimeImmutable;
    }

    public function dateTimeImmutable(): DateTimeImmutable
    {
        return $this->dateTimeImmutable;
    }
}
