<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model\Price;

use Countable;
use hiqdev\php\billing\product\Domain\Model\Price\Exception\InvalidPriceTypeCollectionException;
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
        $this->assertAllPriceTypes($types);
        $this->flippedTypeNames = array_flip($this->names());
    }

    private function assertAllPriceTypes(array $types): void
    {
        foreach ($types as $type) {
            if (!$type instanceof PriceTypeInterface) {
                throw InvalidPriceTypeCollectionException::becauseContainsNonPriceType($type);
            }
        }
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
