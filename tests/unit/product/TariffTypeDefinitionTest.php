<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\Exception\ProductNotDefinedException;
use hiqdev\php\billing\product\Exception\TariffTypeLockedException;
use hiqdev\php\billing\product\TariffTypeDefinition;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\ProductInterface;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollection;
use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use hiqdev\php\billing\type\Type;
use PHPUnit\Framework\TestCase;

class TariffTypeDefinitionTest extends TestCase
{
    public function testTariffType(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $definition = new TariffTypeDefinition($tariffType);

        $this->assertSame($tariffType, $definition->tariffType());
    }

    public function testOfProduct(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $definition = new TariffTypeDefinition($tariffType);

        $this->assertInstanceOf(TariffTypeDefinitionInterface::class, $definition->ofProduct($product));
    }

    public function testGetProduct(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $definition = new TariffTypeDefinition($tariffType);
        $definition->ofProduct($product);

        $this->assertSame($product, $definition->getProduct());
    }

    public function testGetProductThrowsExceptionIfNotSet(): void
    {
        $this->expectException(ProductNotDefinedException::class);

        $tariffType = $this->createMock(TariffTypeInterface::class);
        $definition = new TariffTypeDefinition($tariffType);
        $definition->getProduct();
    }

    public function testWithPrices(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $definition = new TariffTypeDefinition($tariffType);

        $this->assertInstanceOf(PriceTypeDefinitionCollection::class, $definition->withPrices());
    }

    public function testWithBehaviors(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $definition = new TariffTypeDefinition($tariffType);

        $this->assertInstanceOf(BehaviorTariffTypeCollection::class, $definition->withBehaviors());
    }

    public function testLockPreventsModification(): void
    {
        $tariffType = $this->createMock(TariffTypeInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $definition = new TariffTypeDefinition($tariffType);
        $definition->ofProduct($product);
        $definition->withPrices()
            ->priceType(Type::anyId('dummy'));
        $definition->end();

        $this->expectException(TariffTypeLockedException::class);
        $definition->ofProduct($this->createMock(ProductInterface::class));
    }
}
