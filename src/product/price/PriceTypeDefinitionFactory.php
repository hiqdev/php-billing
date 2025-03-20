<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\type\TypeInterface;

class PriceTypeDefinitionFactory implements PriceTypeDefinitionFactoryInterface
{
    public function create(
        PriceTypeDefinitionCollectionInterface $parent,
        TypeInterface $type,
        TariffTypeInterface $tariffType,
    ): PriceTypeDefinition {
        return new PriceTypeDefinition($parent, $type, $tariffType);
    }
}
