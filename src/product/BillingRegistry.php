<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\Exception\AggregateNotFoundException;
use hiqdev\php\billing\product\invoice\InvalidRepresentationException;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\quantity\QuantityFormatterNotFoundException;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;

class BillingRegistry implements BillingRegistryInterface
{
    use HasLock;

    /** @var TariffTypeDefinitionInterface[] */
    private array $tariffTypeDefinitions = [];
    private bool $locked = false;

    public function addTariffType(TariffTypeDefinitionInterface $tariffTypeDefinition): void
    {
        $this->ensureNotLocked();

        $this->tariffTypeDefinitions[] = $tariffTypeDefinition;
    }

    public function priceTypes(): \Generator
    {
        foreach ($this->tariffTypeDefinitions as $tariffTypeDefinition) {
            foreach ($tariffTypeDefinition->withPrices() as $priceTypeDefinition) {
                yield $priceTypeDefinition;
            }
        }
    }

    public function getRepresentationsByType(string $representationClass): array
    {
        if (!class_exists($representationClass)) {
            throw new InvalidRepresentationException("Class '$representationClass' does not exist");
        }

        if (!is_subclass_of($representationClass, RepresentationInterface::class)) {
            throw new InvalidBehaviorException(
                sprintf('Representation class "%s" does not implement RepresentationInterface', $representationClass)
            );
        }

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
    ): QuantityFormatterInterface {
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

    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface
    {
        if (!class_exists($behaviorClassWrapper)) {
            throw new InvalidBehaviorException(
                sprintf('Behavior class "%s" does not exist', $behaviorClassWrapper)
            );
        }

        if (!is_subclass_of($behaviorClassWrapper, BehaviorInterface::class)) {
            throw new InvalidBehaviorException(
                sprintf('Behavior class "%s" does not implement BehaviorInterface', $behaviorClassWrapper)
            );
        }

        $billingType = $this->convertStringTypeToType($type);

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($billingType)) {
                $behavior = $this->findBehaviorInPriceType($priceTypeDefinition, $behaviorClassWrapper);

                if ($behavior) {
                    return $behavior;
                }
            }
        }

        throw new BehaviorNotFoundException(
            sprintf('Behavior of class "%s" not found for type "%s"', $behaviorClassWrapper, $type),
        );
    }

    private function findBehaviorInPriceType(
        PriceTypeDefinition $priceTypeDefinition,
        string $behaviorClassWrapper
    ): ?BehaviorInterface {
        foreach ($priceTypeDefinition->withBehaviors() as $behavior) {
            if ($behavior instanceof $behaviorClassWrapper) {
                return $behavior;
            }
        }

        return null;
    }

    public function getBehaviors(string $behaviorClassWrapper): \Generator
    {
        foreach ($this->tariffTypeDefinitions as $tariffTypeDefinition) {
            foreach ($tariffTypeDefinition->withBehaviors() as $behavior) {
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

    public function getAggregate(string $type): AggregateInterface
    {
        $type = $this->convertStringTypeToType($type);

        foreach ($this->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($type)) {
                return $priceTypeDefinition->getAggregate();
            }
        }

        throw new AggregateNotFoundException('Aggregate was not found');
    }

    public function getTariffTypeDefinitions(): \Generator
    {
        foreach ($this->tariffTypeDefinitions as $tariffTypeDefinition) {
            yield $tariffTypeDefinition;
        }
    }

    protected function afterLock(): void
    {
        $this->lockItems($this->tariffTypeDefinitions);
    }
}
