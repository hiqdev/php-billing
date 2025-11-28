<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\type\TypeInterface;
use InvalidArgumentException;

class PriceTypeStorage implements \Countable
{
    private array $pricesGroupedByPriceType = [];

    private int $i = 0;

    public function add(TypeInterface $type, PriceTypeDefinitionInterface $priceTypeDefinition): void
    {
        $typeName = $type->getName();
        if ($typeName === null) {
            throw new InvalidArgumentException('Price type name must not be null');
        }

        $this->pricesGroupedByPriceType[$typeName][] = $priceTypeDefinition;
        $this->i++;
    }

    /**
     * @return PriceTypeDefinitionInterface[]
     */
    public function getAll(): array
    {
        $allPrices = [];
        foreach ($this->pricesGroupedByPriceType as $prices) {
            foreach ($prices as $price) {
                $allPrices[] = $price;
            }
        }

        return $allPrices;
    }

    public function count(): int
    {
        return $this->i;
    }
}
