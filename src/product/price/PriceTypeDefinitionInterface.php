<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\behavior\HasBehaviorsInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use hiqdev\php\billing\product\invoice\RepresentationCollection;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;
use hiqdev\php\billing\type\TypeInterface;

interface PriceTypeDefinitionInterface extends HasBehaviorsInterface, HasLockInterface
{
    public function unit(UnitInterface $unit): self;

    public function description(string $description): self;

    public function getDescription(): string;

    public function quantityFormatter(string $formatterClass, $fractionUnit = null): self;

    public function createQuantityFormatter(FractionQuantityData $data): QuantityFormatterInterface;

    public function end(): PriceTypeDefinitionCollectionInterface;

    public function documentRepresentation(): RepresentationCollection;

    // TODO: Not sure if it will be needed at all
    public function measuredWith(\hiqdev\billing\registry\measure\RcpTrafCollector $param): self;

    public function type(): TypeInterface;

    public function hasType(TypeInterface $type): bool;

    public function getUnit(): UnitInterface;

    /**
     * це параметер визначає агрегатну функцію яка застосовується для щоденно записаних ресурсів щоб визнизначти
     * місячне споживання за яке потрібно пробілити клієнта
     *
     * @param AggregateInterface $aggregate
     * @return self
     */
    public function aggregation(AggregateInterface $aggregate): self;

    public function getAggregate(): AggregateInterface;

    /**
     * For establishing a relationship between PriceTypeDefinition and TariffTypeDefinition
     *
     * @return TariffTypeDefinitionInterface
     */
    public function getTariffTypeDefinition(): TariffTypeDefinitionInterface;

    public function belongsToTariffType(string $tariffTypeName): bool;

    public function belongsToPriceType(PriceTypeInterface $priceType): bool;
}
