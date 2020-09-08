<?php
declare(strict_types=1);

namespace hiqdev\php\billing\tools;

use DateTimeImmutable;

/**
 * Interface CurrentDateTimeProviderInterface provides a current DateTime.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface CurrentDateTimeProviderInterface
{
    public function dateTimeImmutable(): DateTimeImmutable;
}
