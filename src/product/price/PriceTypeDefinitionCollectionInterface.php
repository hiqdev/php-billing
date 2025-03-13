<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\type\TypeInterface;

interface PriceTypeDefinitionCollectionInterface extends \IteratorAggregate
{
    /**
     * @return PriceTypeDefinition[]
     */
    public function getIterator(): \Traversable;

    public function priceType(TypeInterface $type): PriceTypeDefinition;

    public function end(): TariffTypeDefinitionInterface;
}