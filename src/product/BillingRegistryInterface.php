<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use Generator;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;

interface BillingRegistryInterface extends HasLockInterface
{
    /**
     * @return Generator<PriceTypeDefinitionInterface>
     */
    public function priceTypes(): Generator;

    /**
     * @return TariffTypeDefinitionInterface[]
     */
    public function getTariffTypeDefinitions(): array;

    public function addTariffType(TariffTypeDefinitionInterface $tariffTypeDefinition): void;
}
