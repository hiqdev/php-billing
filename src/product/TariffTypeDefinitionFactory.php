<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

class TariffTypeDefinitionFactory
{
    public static function create(TariffTypeInterface $tariffType): TariffTypeDefinition
    {
        return new TariffTypeDefinition($tariffType);
    }
}
