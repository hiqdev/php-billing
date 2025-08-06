<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\HasBehaviorsInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollectionInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;

/**
 * @template-covariant TPriceTypeDefinitionCollection of PriceTypeDefinitionCollectionInterface
 * @extends HasBehaviorsInterface<static>
 * @psalm-consistent-templates
 * @psalm-suppress InvalidTemplateParam
 */
interface TariffTypeDefinitionInterface extends HasBehaviorsInterface, HasLockInterface
{
    public function tariffType(): TariffTypeInterface;

    /**
     * Check if TariffTypeDefinition belongs to specified TariffType
     *
     * @param TariffTypeInterface $tariffType
     * @return bool
     */
    public function belongToTariffType(TariffTypeInterface $tariffType): bool;

    public function ofProduct(ProductInterface $product): static;

    public function getProduct(): ProductInterface;

    public function setPricesSuggester(string $suggesterClass): static;

    /**
     * @return TPriceTypeDefinitionCollection
     */
    public function withPrices();

    public function end();

    public function findPricesByTypeName(string $typeName): ?array;
}
