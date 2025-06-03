<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Application;

use hiqdev\php\billing\product\Application\BillingRegistryService;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\Exception\AggregateNotFoundException;
use hiqdev\php\billing\product\invoice\InvalidRepresentationException;
use hiqdev\php\billing\product\TariffTypeDefinition;
use hiqdev\php\billing\tests\unit\product\behavior\FakeBehavior;
use hiqdev\php\billing\tests\unit\product\behavior\TestBehavior;
use hiqdev\php\billing\tests\unit\product\Domain\Model\DummyTariffType;
use hiqdev\php\billing\tests\unit\product\Domain\Model\FakeTariffType;
use hiqdev\php\billing\type\Type;
use PHPUnit\Framework\TestCase;

class BillingRegistryServiceTest extends TestCase
{
    private BillingRegistry $registry;

    private BillingRegistryService $registryService;

    protected function setUp(): void
    {
        $this->registry = new BillingRegistry();
        $this->registryService = new BillingRegistryService($this->registry);
    }

    public function testGetRepresentationsByTypeThrowsOnInvalidClass(): void
    {
        $this->expectException(InvalidRepresentationException::class);
        $this->registryService->getRepresentationsByType('InvalidClass');
    }

    public function testGetAggregateThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(AggregateNotFoundException::class);
        $this->registryService->getAggregate('non-existent-type');
    }

    //    public function testCreateQuantityFormatterThrowsExceptionWhenNotFound(): void
//    {
//        $this->expectException(QuantityFormatterNotFoundException::class);
//        $this->registryService->createQuantityFormatter('non-existent-type', $this->createMock(FractionQuantityData::class));
//    }
}
