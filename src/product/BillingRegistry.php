<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\trait\HasLock;

class BillingRegistry implements BillingRegistryInterface
{
    use HasLock;

    /** @var TariffTypeDefinitionInterface[] */
    private array $tariffTypeDefinitions = [];

    public function addTariffType(TariffTypeDefinitionInterface $tariffTypeDefinition): void
    {
        $this->ensureNotLocked();

        $this->tariffTypeDefinitions[] = $tariffTypeDefinition;
    }

    public function getTariffTypeDefinitions(): array
    {
        return $this->tariffTypeDefinitions;
    }

    public function priceTypes(): \Generator
    {
        foreach ($this->getTariffTypeDefinitions() as $tariffTypeDefinition) {
            foreach ($tariffTypeDefinition->withPrices() as $priceTypeDefinition) {
                yield $priceTypeDefinition;
            }
        }
    }

    protected function afterLock(): void
    {
        $this->lockItems($this->tariffTypeDefinitions);
    }
}
