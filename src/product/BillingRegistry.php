<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\invoice\RepresentationInterface;

class BillingRegistry implements BillingRegistryInterface
{
    /** @var TariffType[] */
    private array $tariffTypes = [];
    private bool $locked = false;

    public function addTariffType(TariffType $tariffType): void
    {
        if ($this->locked) {
            throw new \RuntimeException("BillingRegistry is locked and cannot be modified.");
        }

        $this->tariffTypes[] = $tariffType;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function priceTypes(): \Generator
    {
        foreach ($this->tariffTypes as $tariffType) {
            foreach ($tariffType->withPrices() as $priceTypeDefinition) {
                yield $priceTypeDefinition;
            }
        }
    }

    /**
     * @param string $representationClass
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array
    {
        $representations = [];
        foreach ($this->priceTypes() as $priceTypeDefinition) {
            foreach ($priceTypeDefinition->documentRepresentation() as $representation) {
                if ($representation instanceof $representationClass) {
                    $representations[] = $representation;
                }
            }
        }

        return $representations;
    }
}
