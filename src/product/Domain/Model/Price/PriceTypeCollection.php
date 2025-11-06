<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model\Price;

use Countable;
use IteratorAggregate;
use Traversable;

class PriceTypeCollection implements IteratorAggregate, Countable
{
    /**
     * @var string[] - flipped type names for fast search
     */
    private array $flippedTypeNames;

    public function __construct(private readonly array $types = [])
    {
        $this->flippedTypeNames = array_flip($this->names());
    }

    public function names(): array
    {
        return array_map(fn(PriceTypeInterface $t) => $t->name(), $this->types);
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
        return array_key_exists($priceType, $this->flippedTypeNames);
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
