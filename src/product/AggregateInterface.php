<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface AggregateInterface
{
    public function isMax(): bool;
}
