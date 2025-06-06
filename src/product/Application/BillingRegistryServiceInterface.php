<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;

interface BillingRegistryServiceInterface extends BillingRegistryTariffServiceInterface, BillingRegistryBehaviorServiceInterface
{
    /**
     * @param string $representationClass
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array;

    public function createQuantityFormatter(string $type, FractionQuantityData $data): QuantityFormatterInterface;

    public function getAggregate(string $type): AggregateInterface;
}
