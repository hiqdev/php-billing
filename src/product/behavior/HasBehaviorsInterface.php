<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

/**
 * @template TParentCollection
 * @psalm-consistent-templates
 */
interface HasBehaviorsInterface
{
//    /**
//     * @return BehaviorCollectionInterface<TParentCollection>
//     */
//    public function withBehaviors();

    public function hasBehavior(string $behaviorClassName): bool;
}
