<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\trait\HasLock;

class BillingRegistry implements BillingRegistryInterface
{
    use HasLock;

    /** @var TariffTypeDefinitionInterface[] */
    private array $tariffTypeDefinitions = [];

    private BillingRegistryService $service;

    public function __construct()
    {
        $this->service = new BillingRegistryService($this);
    }

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

    public function getRepresentationsByType(string $representationClass): array
    {
        return $this->service->getRepresentationsByType($representationClass);
    }

    public function createQuantityFormatter(
        string $type,
        FractionQuantityData $data,
    ): QuantityFormatterInterface {
        return $this->service->createQuantityFormatter($type, $data);
    }

    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface
    {
        return $this->service->getBehavior($type, $behaviorClassWrapper);
    }

    /**
     * @inerhitDoc
     */
    public function getBehaviors(string $behaviorClassWrapper): \Generator
    {
        return $this->service->getBehaviors($behaviorClassWrapper);
    }

    public function getAggregate(string $type): AggregateInterface
    {
        return $this->service->getAggregate($type);
    }

    public function findTariffTypeDefinitionByBehavior(BehaviorInterface $behavior): TariffTypeDefinitionInterface
    {
        return $this->service->findTariffTypeDefinitionByBehavior($behavior);
    }

    public function findPriceTypeDefinitionsByBehavior(string $behaviorClassWrapper): \Generator
    {
        return $this->service->findPriceTypeDefinitionsByBehavior($behaviorClassWrapper);
    }

    protected function afterLock(): void
    {
        $this->lockItems($this->tariffTypeDefinitions);
    }
}
