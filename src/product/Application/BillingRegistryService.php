<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\BillingRegistryInterface;
use hiqdev\php\billing\product\Exception\PriceTypeDefinitionNotFoundException;
use hiqdev\php\billing\product\Exception\TariffTypeDefinitionNotFoundException;
use hiqdev\php\billing\product\invoice\InvalidRepresentationException;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;

final class BillingRegistryService implements BillingRegistryServiceInterface
{
    public function __construct(private readonly BillingRegistryInterface $registry)
    {
    }

    public function getRepresentationsByType(string $representationClass): array
    {
        if (!class_exists($representationClass) && !interface_exists($representationClass)) {
            throw new InvalidRepresentationException("Class '$representationClass' does not exist");
        }

        if (class_exists($representationClass)
            && !is_subclass_of($representationClass, RepresentationInterface::class)
        ) {
            throw new InvalidBehaviorException(
                sprintf(
                    'Representation class "%s" does not implement RepresentationInterface',
                    $representationClass,
                )
            );
        }

        $representations = [];
        foreach ($this->registry->priceTypes() as $priceTypeDefinition) {
            foreach ($priceTypeDefinition->documentRepresentation() as $representation) {
                if ($representation instanceof $representationClass) {
                    $representations[] = $representation;
                }
            }
        }

        return $representations;
    }

    public function getTariffTypeDefinitionByTariffName(string $tariffName): TariffTypeDefinitionInterface
    {
        foreach ($this->registry->getTariffTypeDefinitions() as $tariffTypeDefinition) {
            if ($tariffTypeDefinition->tariffType()->equalsName($tariffName)) {
                return $tariffTypeDefinition;
            }
        }

        throw new TariffTypeDefinitionNotFoundException('Tariff type definition was not found');
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

        foreach ($this->registry->priceTypes() as $priceTypeDefinition) {
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

    private function convertStringTypeToType(string $type): TypeInterface
    {
        return Type::anyId($type);
    }

    private function findBehaviorInPriceType(
        PriceTypeDefinitionInterface $priceTypeDefinition,
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
        foreach ($this->registry->getTariffTypeDefinitions() as $tariffTypeDefinition) {
            foreach ($tariffTypeDefinition->withBehaviors() as $behavior) {
                if ($behavior instanceof $behaviorClassWrapper) {
                    yield $behavior;
                }
            }
        }

        foreach ($this->registry->priceTypes() as $priceTypeDefinition) {
            foreach ($priceTypeDefinition->withBehaviors() as $behavior) {
                if ($behavior instanceof $behaviorClassWrapper) {
                    yield $behavior;
                }
            }
        }
    }

    public function getAggregate(string $type): AggregateInterface
    {
        return $this->getPriceTypeDefinitionByPriceTypeName($type)->getAggregate();
    }

    public function getPriceTypeDefinitionByPriceTypeName(string $typeName): PriceTypeDefinitionInterface
    {
        $type = $this->convertStringTypeToType($typeName);

        foreach ($this->registry->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasType($type)) {
                return $priceTypeDefinition;
            }
        }

        throw new PriceTypeDefinitionNotFoundException(sprintf(
            'PriceTypeDefinition was not found for %s type',
            $typeName,
        ));
    }

    public function findPriceTypeDefinitionsByBehavior(string $behaviorClassWrapper): \Generator
    {
        foreach ($this->registry->priceTypes() as $priceTypeDefinition) {
            if ($priceTypeDefinition->hasBehavior($behaviorClassWrapper)) {
                yield $priceTypeDefinition;
            }
        }
    }
}
