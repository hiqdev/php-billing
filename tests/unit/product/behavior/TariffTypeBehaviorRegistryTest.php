<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\behavior;

use hiqdev\php\billing\product\behavior\TariffTypeBehaviorRegistry;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use PHPUnit\Framework\TestCase;

class TariffTypeBehaviorRegistryTest extends TestCase
{
    private TariffTypeBehaviorRegistry $manager;

    protected function setUp(): void
    {
        $tariffTypeDefinition = $this->createMock(TariffTypeDefinitionInterface::class);
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $this->manager = new TariffTypeBehaviorRegistry($tariffTypeDefinition, $tariffType);
    }

    public function testWithBehaviorsReturnsBehaviorCollection(): void
    {
        $this->assertInstanceOf(BehaviorTariffTypeCollection::class, $this->manager->getBehaviors());
    }

    public function testHasBehaviorReturnsFalseWhenBehaviorNotPresent(): void
    {
        $this->assertFalse($this->manager->hasBehavior(TestBehavior::class));
    }

    public function testHasBehaviorReturnsTrueWhenBehaviorPresent(): void
    {
        $behavior = $this->createMock(TestBehavior::class);
        $behaviorCollection = $this->manager->getBehaviors();
        $behaviorCollection->attach($behavior);

        $this->assertTrue($this->manager->hasBehavior(TestBehavior::class));
    }
}
