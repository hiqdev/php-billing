<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\invoice\RepresentationCollection;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

/**
 * @template TParentCollection of PriceTypeDefinitionInterface|TariffTypeDefinitionInterface
 * @psalm-consistent-templates
 */
interface HasBehaviorsInterface
{
    /**
     * @return BehaviorCollectionInterface<TParentCollection>
     */
    public function withBehaviors();

    public function hasBehavior(string $behaviorClassName): bool;

    public function findBehaviorByClass(string $class): ?BehaviorInterface;
}
