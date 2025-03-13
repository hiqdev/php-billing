<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollectionInterface;

interface TariffTypeDefinitionInterface extends ParentNodeDefinitionInterface
{
    public function tariffType(): TariffTypeInterface;

    public function ofProduct(ProductInterface $product): self;

    public function setPricesSuggester(string $suggesterClass): self;

    public function withPrices(): PriceTypeDefinitionCollectionInterface;

    public function end(): self;
}