<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\invoice\RepresentationInterface;
use hiqdev\billing\registry\product\Aggregate;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterNotFoundException;
use hiqdev\billing\registry\quantity\FractionQuantityData;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;

class BillingRegistry implements BillingRegistryInterface
{
    /** @var TariffTypeDefinition[] */
    private array $tariffTypes = [];
    private bool $locked = false;

    public function addTariffType(TariffTypeDefinition $tariffType): void
    {
        if ($this->locked) {
            throw new \RuntimeException("BillingRegistry is locked and cannot be modified.");
        }

        $this->tariffTypes[] = $tariffType;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function priceTypes(): \Generator
    {
        foreach ($this->tariffTypes as $tariffType) {
            foreach ($tariffType->withPrices() as $priceTypeDefinition) {
                yield $priceTypeDefinition;
            }
        }
    }

    /**
     * @param string $representationClass
     * @return RepresentationInterface[]
     */
    public function getRepresentationsByType(string $representationClass): array
    {
        $representations = [];
        foreach ($this->priceTypes() as $priceTypeDefinition) {
            foreach ($priceTypeDefinition->documentRepresentation() as $representation) {
                if ($representation instanceof $representationClass) {
                    $representations[] = $representation;
                }
            }
        }

        return $representations;
    }

    public function createQuantityFormatter(
        string $type,
        FractionQuantityData $data,
    ): array {
        $type = $this->convertStringTypeToType($type);

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($type)) {
                return $priceTypeDefinition->createQuantityFormatter($data);
            }
        }

        throw new QuantityFormatterNotFoundException('Quantity formatter not found');
    }

    private function convertStringTypeToType(string $type): TypeInterface
    {
        return Type::anyId($type);
    }

    /**
     * @param string $type - full type like 'overuse,lb_capacity_unit'
     * @param string $behaviorClassWrapper
     * @return BehaviorInterface
     * @throws BehaviorNotFoundException
     */
    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface
    {
        $type = $this->convertStringTypeToType($type);

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($type)) {
                foreach ($priceTypeDefinition->withBehaviors() as $behavior) {
                    if ($behavior instanceof $behaviorClassWrapper) {
                        return $behavior;
                    }
                }
            }
        }

        throw new BehaviorNotFoundException('Behaviour was not found');
    }

    public function getBehaviors(string $behaviorClassWrapper): \Generator
    {
        foreach ($this->tariffTypes as $tariffType) {
            foreach ($tariffType->withBehaviors() as $behavior) {
                if ($behavior instanceof $behaviorClassWrapper) {
                    yield $behavior;
                }
            }
        }

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            foreach ($priceTypeDefinition->withBehaviors() as $behavior) {
                if ($behavior instanceof $behaviorClassWrapper) {
                    yield $behavior;
                }
            }
        }
    }

    public function getAggregate(string $type): Aggregate
    {
        $type = $this->convertStringTypeToType($type);

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($type)) {
                return $priceTypeDefinition->getAggregate();
            }
        }

        throw new AggregateNotFoundException('Aggregate was not found');
    }
}
