<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

abstract class TariffType implements TariffTypeInterface
{
    public function equals(TariffTypeInterface $tariffType): bool
    {
        return true;
    }

    public function equalsName(string $tariffTypeName): bool
    {
        return true;
    }
}
