<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hiqdev\php\billing\product\price\PriceTypeDefinition;

/**
 * @template T of PriceTypeDefinition
 */
class InvoiceRepresentationCollection implements \IteratorAggregate
{
    private array $representations = [];

    public function __construct(private readonly PriceTypeDefinition $priceTypeDefinition)
    {
    }

    /**
     * @return RepresentationInterface[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->representations);
    }

    public function attach(RepresentationInterface $representation): self
    {
        $representation->setType($this->priceTypeDefinition->type());

        $this->representations[] = $representation;

        return $this;
    }

    /**
     * @psalm-return T
     */
    public function end(): PriceTypeDefinition
    {
        return $this->priceTypeDefinition;
    }

    public function filterByType(string $className): array
    {
        return array_filter($this->representations, fn($r) => $r instanceof $className);
    }
}
