<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;

/**
 * @template T as PriceTypeDefinitionInterface
 * @extends BehaviorRegistry<PriceTypeDefinitionInterface>
 */
final class PriceTypeBehaviourRegistry extends BehaviorRegistry
{
    /**
     * @var BehaviorPriceTypeDefinitionCollection<T>
     */
    private BehaviorPriceTypeDefinitionCollection $behaviorCollection;

    /**
     * @psalm-param T $tariffTypeDefinition
     */
    public function __construct(PriceTypeDefinitionInterface $tariffTypeDefinition, TariffTypeInterface $tariffType)
    {
        $this->behaviorCollection = new BehaviorPriceTypeDefinitionCollection($tariffTypeDefinition, $tariffType);
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
