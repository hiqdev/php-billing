<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\PriceTypesCollection;
use hiqdev\php\billing\product\TariffType;
use PHPUnit\Framework\TestCase;

class TariffTypeTest extends TestCase
{
    public function testTariffTypeInitialization(): void
    {
        $tariffType = new TariffType('server');

        $this->assertSame('server', $this->getPrivateProperty($tariffType, 'name'), 'TariffType name should be initialized correctly.');
        $this->assertInstanceOf(PriceTypesCollection::class, $this->getPrivateProperty($tariffType, 'prices'), 'Prices should be an instance of PriceTypesCollection.');
    }

    public function testOfProduct(): void
    {
        $tariffType = new TariffType('server');
        $tariffType->ofProduct('ServerProductClass');

        $this->assertSame(
            'ServerProductClass',
            $this->getPrivateProperty($tariffType, 'productClass'),
            'Product class should be set correctly.'
        );
    }

    public function testAttachBehavior(): void
    {
        $tariffType = new TariffType('server');
        $behavior = new OncePerMonthPlanChangeBehavior();
        $tariffType->attach($behavior);

        $behaviors = $this->getPrivateProperty($tariffType, 'behaviors');

        $this->assertCount(1, $behaviors, 'Behavior should be added to the behaviors list.');
        $this->assertSame($behavior, $behaviors[0], 'Behavior should match the attached instance.');
    }

    public function testPricesCollectionInteraction(): void
    {
        $tariffType = new TariffType('server');
        $prices = $tariffType->withPrices();

        $this->assertInstanceOf(PriceTypesCollection::class, $prices, 'withPrices() should return a PriceTypesCollection instance.');

        $priceType = $prices->monthly('support_time');
        $priceType->unit('hour')->description('Monthly fee for support time');
        $priceType->end();

        $this->assertNotEmpty($this->getPrivateProperty($prices, 'prices'), 'PriceTypesCollection should contain defined price types.');
    }

    public function testEndLocksTariffType(): void
    {
        $tariffType = new TariffType('server');
        $tariffType->end();

        // Assuming TariffType has a `locked` private property
        $isLocked = $this->getPrivateProperty($tariffType, 'locked');
        $this->assertTrue($isLocked, 'TariffType should be locked after calling end().');
    }

    /**
     * Helper function to access private properties for testing.
     */
    private function getPrivateProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
