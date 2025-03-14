<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class TariffTypeDefinitionFactory
{
    public static function create(TariffTypeInterface $tariffType): TariffTypeDefinition
    {
        return new TariffTypeDefinition($tariffType);
    }
}
