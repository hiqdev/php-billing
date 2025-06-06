<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use Generator;
use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

interface BillingRegistryServiceInterface extends BillingRegistryTariffServiseInterface, BillingRegistryBehaviorServiceInterface
{
    /**
     * @param string $representationClass
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array;

    public function createQuantityFormatter(string $type, FractionQuantityData $data): QuantityFormatterInterface;

    public function getAggregate(string $type): AggregateInterface;

}
