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
final readonly class CachedDateTimeProvider implements CurrentDateTimeProviderInterface
{
    public function __construct(private DateTimeImmutable $dateTimeImmutable)
    {
    }

    public function dateTimeImmutable(): DateTimeImmutable
    {
        return $this->dateTimeImmutable;
    }
}
