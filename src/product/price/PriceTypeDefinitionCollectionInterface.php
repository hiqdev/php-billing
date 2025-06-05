<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use Countable;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;
use hiqdev\php\billing\type\TypeInterface;
use IteratorAggregate;
use Traversable;

/**
 * @template TPriceTypeDefinition of PriceTypeDefinitionInterface
 * @extends IteratorAggregate<int, PriceTypeDefinitionInterface>
 * @psalm-consistent-templates
 */
interface PriceTypeDefinitionCollectionInterface extends IteratorAggregate, Countable, HasLockInterface
{
    /**
     * @return Traversable<int, PriceTypeDefinitionInterface>
     */
    public function getIterator(): Traversable;

    /**
     * @psalm-return PriceTypeDefinitionInterface<static, TPriceTypeDefinition>
     */
    public function priceType(TypeInterface $type): PriceTypeDefinitionInterface;

    /**
     * @return TariffTypeDefinitionInterface
     * @plsam-return M
     */
    public function end(): TariffTypeDefinitionInterface;

    /**
     * For easier understanding and establishing a relationship between PriceTypeDefinitionCollection
     * and TariffTypeDefinition
     *
     * @return TariffTypeDefinitionInterface
     * @plsam-return M
     */
    public function getTariffTypeDefinition(): TariffTypeDefinitionInterface;
}
