<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use Generator;
use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\Exception\PriceTypeDefinitionNotFoundException;
use hiqdev\php\billing\product\Exception\TariffTypeDefinitionNotFoundException;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

interface BillingRegistryServiceInterface
{
    /**
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array;

    #[\Deprecated(message: '- please use getPriceTypeDefinitionByPriceTypeName() method instead')]
    public function getAggregate(string $type): AggregateInterface;

    /**
     * @throws PriceTypeDefinitionNotFoundException
     */
    public function getPriceTypeDefinitionByPriceTypeName(string $typeName): PriceTypeDefinitionInterface;

    /**
     * @throws TariffTypeDefinitionNotFoundException
     */
    public function getTariffTypeDefinitionByTariffName(string $tariffName): TariffTypeDefinitionInterface;

    /**
     * @param string $type - full type like 'overuse,lb_capacity_unit'
     * @throws BehaviorNotFoundException
     * @throws InvalidBehaviorException
     */
    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface;


    /**
     * Find all behaviors attached to any TariffType or PriceType by specified Behavior class.
     *
     * @return Generator<BehaviorInterface>
     */
    public function getBehaviors(string $behaviorClassWrapper): Generator;

    /**
     * Find all PriceTypeDefinition in registry by specified Behavior class.
     *
     * @return Generator<PriceTypeDefinitionInterface>
     */
    public function findPriceTypeDefinitionsByBehavior(string $behaviorClassWrapper): Generator;
}
