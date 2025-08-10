<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;

/**
 * @template T as PriceTypeDefinitionInterface
 * @extends BehaviorRegistry<T>
 */
final class PriceTypeBehaviorRegistry extends BehaviorRegistry
{
    /**
     * @var BehaviorPriceTypeDefinitionCollection<T>
     */
    private BehaviorPriceTypeDefinitionCollection $behaviorCollection;

    /**
     * @psalm-param T $priceTypeDefinition
     */
    public function __construct(PriceTypeDefinitionInterface $priceTypeDefinition, TariffTypeInterface $tariffType)
    {
        $this->behaviorCollection = new BehaviorPriceTypeDefinitionCollection($priceTypeDefinition, $tariffType);
    }

    /**
     * @return BehaviorPriceTypeDefinitionCollection<T>
     */
    public function withBehaviors(): BehaviorPriceTypeDefinitionCollection
    {
        return $this->behaviorCollection;
    }

    protected function getBehaviorCollection(): BehaviorCollectionInterface
    {
        return $this->behaviorCollection;
    }
}
