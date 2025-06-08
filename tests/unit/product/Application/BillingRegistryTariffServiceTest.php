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

class BillingRegistryTariffServiceTest extends TestCase
{
    private BillingRegistry $registry;

    private BillingRegistryService $registryService;

    protected function setUp(): void
    {
        $this->registry = new BillingRegistry();
        $this->registryService = new BillingRegistryService($this->registry);
    }

    public function testGetTariffDefinitionByName(): void
    {
        $tariffType = new DummyTariffType();
        $tariffTypeDefinition = new TariffTypeDefinition($tariffType);
        $dummyBehavior = new TestBehavior('dummy');
        $tariffTypeDefinition
            ->withBehaviors()
            ->attach($dummyBehavior);

        $this->registry->addTariffType($tariffTypeDefinition);

        $tariff = $this->registryService->getTariffTypeDefinitionByName('dummy');

        $this->assertSame($tariffType->name(), $tariff->tariffType()->name());
    }
}
