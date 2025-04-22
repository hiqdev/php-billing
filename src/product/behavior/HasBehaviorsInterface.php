<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

interface HasBehaviorsInterface
{
    public function withBehaviors(): BehaviorCollectionInterface;

    public function hasBehavior(string $behaviorClassName): bool;
}
