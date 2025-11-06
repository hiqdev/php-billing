<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use Countable;
use IteratorAggregate;
use Traversable;

class PriceTypeCollection implements IteratorAggregate, Countable
{
    /**
     * @var string[] - flipped types for fast search
     */
    private array $flippedTypes;

    public function __construct(private readonly array $types = [])
    {
        $this->flippedTypes = array_flip(array_map(fn(PriceTypeInterface $t) => $t->name(), $types));
    }

    /**
     * @return Traversable<int, PriceTypeInterface>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->types);
    }

    public function has(string $priceType): bool
    {
        return array_key_exists($priceType, $this->flippedTypes);
    }

    public function count(): int
    {
        return count($this->types);
    }

    public function hasItems(): bool
    {
        return $this->count() > 0;
    }
}
