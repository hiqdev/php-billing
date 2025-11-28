<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\Exception\LockedException;
use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\tests\unit\product\Domain\Model\DummyTariffType;
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

        $this->expectException(LockedException::class);

        $this->registry->addTariffType($this->tariffTypeDefinition);
    }
}
