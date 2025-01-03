<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface ParentNodeDefinitionInterface
{
    public function withBehaviors(): BehaviorCollection;

    public function hasBehavior(string $behaviorClassName): bool;
}