<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\Exception\BillingRegistryLockedException;
use hiqdev\php\billing\product\TariffTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\Exception\AggregateNotFoundException;
use hiqdev\php\billing\product\invoice\InvalidRepresentationException;
use hiqdev\php\billing\product\quantity\QuantityFormatterNotFoundException;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\type\Type;
use PHPUnit\Framework\TestCase;

class BillingRegistryTest extends TestCase
{
    private BillingRegistry $registry;

    private TariffTypeDefinitionInterface $tariffTypeDefinition;

    protected function setUp(): void
    {
        $this->registry = new BillingRegistry();
        $this->tariffTypeDefinition = new TariffTypeDefinition(new DummyTariffType());
    }

    public function testAddTariffTypeAndRetrievePriceTypes(): void
    {
        $type = Type::anyId('dummy');

        $this->tariffTypeDefinition->withPrices()
            ->priceType($type);

        $this->registry->addTariffType($this->tariffTypeDefinition);

        $priceTypes = iterator_to_array($this->registry->priceTypes());

        $this->assertCount(1, $priceTypes);
        $this->assertSame($this->tariffTypeDefinition, $priceTypes[0]);
    }

    public function testLockPreventsModification(): void
    {
        $this->registry->lock();

        $this->expectException(BillingRegistryLockedException::class);

        $this->registry->addTariffType($this->tariffTypeDefinition);
    }

    public function testGetRepresentationsByTypeThrowsOnInvalidClass(): void
    {
        $this->expectException(InvalidRepresentationException::class);
        $this->registry->getRepresentationsByType('InvalidClass');
    }

    public function testGetAggregateThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(AggregateNotFoundException::class);
        $this->registry->getAggregate('non-existent-type');
    }

    public function testGetBehavior(): void
    {
        $tariffType = new DummyTariffType();
        $tariffTypeDefinition = new TariffTypeDefinition($tariffType);
        $dummyBehavior = new DummyBehavior('dummy');
        $tariffTypeDefinition->withBehaviors()->attach($dummyBehavior);
        $this->registry->addTariffType($tariffTypeDefinition);  // monthly()

        $behavior = $this->registry->getBehavior($tariffType->name(), DummyBehavior::class);

        $this->assertSame($dummyBehavior->getContext(), $behavior->getContext());
    }

    public function testGetBehaviorThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(BehaviorNotFoundException::class);
        $this->registry->getBehavior('non-existent-type', BehaviorInterface::class);
    }

//    public function testCreateQuantityFormatterThrowsExceptionWhenNotFound(): void
//    {
//        $this->expectException(QuantityFormatterNotFoundException::class);
//        $this->registry->createQuantityFormatter('non-existent-type', $this->createMock(FractionQuantityData::class));
//    }
}
