<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use Generator;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;

interface BillingRegistryInterface extends HasLockInterface
{
    /**
     * @return Generator
     * @psalm-return Generator<PriceTypeDefinitionInterface>
     */
    public function priceTypes(): Generator;

    public function addTariffType(TariffTypeDefinitionInterface $tariffTypeDefinition): void;

    /**
     * @param string $representationClass
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array;

    public function createQuantityFormatter(string $type, FractionQuantityData $data): QuantityFormatterInterface;

    /**
     * @param string $type - full type like 'overuse,lb_capacity_unit'
     * @param string $behaviorClassWrapper
     * @return BehaviorInterface
     * @throws BehaviorNotFoundException
     * @throws InvalidBehaviorException
     */
    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface;

    /**
     * @param string $behaviorClassWrapper
     * @return Generator
     * @psalm-return Generator<BehaviorInterface>
     */
    public function getBehaviors(string $behaviorClassWrapper): Generator;

    public function getAggregate(string $type): AggregateInterface;

    /**
     * @return Generator
     * @psalm-return Generator<TariffTypeDefinitionInterface>
     */
    public function getTariffTypeDefinitions(): Generator;
}
