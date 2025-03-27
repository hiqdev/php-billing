<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\Exception\BillingRegistryLockedException;
use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\Exception\AggregateNotFoundException;
use hiqdev\php\billing\product\invoice\InvalidRepresentationException;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\tests\unit\product\behavior\FakeBehavior;
use hiqdev\php\billing\tests\unit\product\behavior\TestBehavior;
use hiqdev\php\billing\tests\unit\product\Domain\Model\DummyTariffType;
use hiqdev\php\billing\tests\unit\product\Domain\Model\FakeTariffType;
use hiqdev\php\billing\type\Type;
use PHPUnit\Framework\TestCase;

final class BillingRegistryTest extends TestCase
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
        /** @var PriceTypeDefinition $priceTypeDefinition */
        $priceTypeDefinition = $priceTypes[0];

        $this->assertSame($type, $priceTypeDefinition->type());
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
        $dummyBehavior = new TestBehavior('dummy');
        $type = Type::anyId('dummy');
        $tariffTypeDefinition
            ->withPrices()
                ->priceType($type)
                ->withBehaviors()
                    ->attach($dummyBehavior);

        $this->registry->addTariffType($tariffTypeDefinition);

        $behavior = $this->registry->getBehavior($type->getName(), TestBehavior::class);

        $this->assertSame($dummyBehavior->getContext(), $behavior->getContext());
    }

    public function testGetBehavior_WithMultipleTariffTypeDefinitions(): void
    {
        $tariffType = new DummyTariffType();
        $tariffTypeDefinition = new TariffTypeDefinition($tariffType);
        $type1 = Type::anyId('type,dummy1');
        $type2 = Type::anyId('type,dummy2');
        $dummyBehavior1 = new TestBehavior('dummy 1');
        $dummyBehavior2 = new TestBehavior('dummy 2');
        $dummyBehavior3 = new FakeBehavior('dummy 3');

        $tariffTypeDefinition
            ->withPrices()
                ->priceType($type1)
                    ->withBehaviors()
                        ->attach($dummyBehavior1)
                    ->end()
                ->end()
                ->priceType($type2)
                    ->withBehaviors()
                        ->attach($dummyBehavior2)
                        ->attach($dummyBehavior3)
                    ->end()
                ->end()
            ->end();

        $this->registry->addTariffType($tariffTypeDefinition);

        $behavior = $this->registry->getBehavior($type1->getName(), TestBehavior::class);
        $this->assertSame($dummyBehavior1->getContext(), $behavior->getContext());

        $behavior = $this->registry->getBehavior($type2->getName(), TestBehavior::class);
        $this->assertSame($dummyBehavior2->getContext(), $behavior->getContext());

        $behavior = $this->registry->getBehavior($type2->getName(), FakeBehavior::class);
        $this->assertSame($dummyBehavior3->getContext(), $behavior->getContext());
    }

    public function testGetBehavior_WithMultiplePriceTypeDefinitions(): void
    {
        $tariffTypeDefinition1 = new TariffTypeDefinition(new DummyTariffType());
        $testBehavior = new TestBehavior('dummy');
        $type1 = Type::anyId('type,dummy1');
        $tariffTypeDefinition1
            ->withPrices()
                ->priceType($type1)
                    ->withBehaviors()
                        ->attach($testBehavior)
                    ->end()
                ->end()
            ->end();

        $tariffTypeDefinition2 = new TariffTypeDefinition(new FakeTariffType());
        $fakeBehavior = new FakeBehavior('dummy');
        $type2 = Type::anyId('type,dummy2');
        $tariffTypeDefinition2
            ->withPrices()
                ->priceType($type2)
                    ->withBehaviors()
                        ->attach($fakeBehavior)
                    ->end()
                ->end()
            ->end();

        $this->registry->addTariffType($tariffTypeDefinition1);
        $this->registry->addTariffType($tariffTypeDefinition2);

        /** @var TestBehavior $testBehaviorActual */
        $testBehaviorActual = $this->registry->getBehavior($type1->getName(), TestBehavior::class);
        $this->assertSame($testBehavior->getContext(), $testBehaviorActual->getContext());

        /** @var FakeBehavior $fakeBehaviorActual */
        $fakeBehaviorActual = $this->registry->getBehavior($type2->getName(), FakeBehavior::class);
        $this->assertSame($fakeBehavior->getContext(), $fakeBehaviorActual->getContext());
    }

    public function testGetBehaviorThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(BehaviorNotFoundException::class);
        $this->registry->getBehavior('non-existent-type', TestBehavior::class);
    }

//    public function testCreateQuantityFormatterThrowsExceptionWhenNotFound(): void
//    {
//        $this->expectException(QuantityFormatterNotFoundException::class);
//        $this->registry->createQuantityFormatter('non-existent-type', $this->createMock(FractionQuantityData::class));
//    }
}
