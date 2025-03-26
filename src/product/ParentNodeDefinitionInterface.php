<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\BehaviorCollectionInterface;

interface ParentNodeDefinitionInterface
{
    public function withBehaviors(): BehaviorCollectionInterface;

    public function hasBehavior(string $behaviorClassName): bool;
}
