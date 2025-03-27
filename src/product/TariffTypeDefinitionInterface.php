<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\HasBehaviorsInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollectionInterface;

/**
 * @template T of PriceTypeDefinitionCollectionInterface
 */
interface TariffTypeDefinitionInterface extends HasBehaviorsInterface
{
    public function tariffType(): TariffTypeInterface;

    public function ofProduct(ProductInterface $product): self;

    public function getProduct(): ProductInterface;

    public function setPricesSuggester(string $suggesterClass): self;

    /**
     * @return PriceTypeDefinitionCollectionInterface
     * @psalm-return T
     */
    public function withPrices(): PriceTypeDefinitionCollectionInterface;

    public function end(): self;
}
