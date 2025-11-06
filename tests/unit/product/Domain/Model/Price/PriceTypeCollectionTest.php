<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model\Price;

use hiqdev\php\billing\product\Domain\Model\Price\Exception\InvalidPriceTypeCollectionException;
use hiqdev\php\billing\product\Domain\Model\Price\PriceTypeCollection;
use hiqdev\php\billing\product\Domain\Model\Price\PriceTypeInterface;
use PHPUnit\Framework\TestCase;

class PriceTypeCollectionTest extends TestCase
{
    public function testEmptyCollection(): void
    {
        $collection = new PriceTypeCollection([]);

        $this->assertCount(0, $collection);
        $this->assertFalse($collection->hasItems());
        $this->assertFalse($collection->has('any'));
    }

    public function testCountAndHasItems(): void
    {
        $type = $this->createPriceType('hourly');
        $collection = new PriceTypeCollection([$type]);

        $this->assertCount(1, $collection);
        $this->assertTrue($collection->hasItems());
    }

    private function createPriceType(string $name): PriceTypeInterface
    {
        return new class($name) implements PriceTypeInterface {
            public function __construct(private string $name) {}
            public function name(): string { return $this->name; }
        };
    }

    public function testHasReturnsTrueForExistingType(): void
    {
        $hourly = $this->createPriceType('hourly');
        $monthly = $this->createPriceType('monthly');

        $collection = new PriceTypeCollection([$hourly, $monthly]);

        $this->assertTrue($collection->has('hourly'));
        $this->assertTrue($collection->has('monthly'));
        $this->assertFalse($collection->has('discount'));
    }

    public function testIteratorReturnsAllTypes(): void
    {
        $types = [
            $this->createPriceType('hourly'),
            $this->createPriceType('fixed'),
        ];

        $collection = new PriceTypeCollection($types);
        $collectedNames = [];

        foreach ($collection as $type) {
            $this->assertInstanceOf(PriceTypeInterface::class, $type);
            $collectedNames[] = $type->name();
        }

        $this->assertSame(['hourly', 'fixed'], $collectedNames);
    }

    public function testHandlesDuplicateNamesGracefully(): void
    {
        // Duplicates in the array should still work for iteration, though flipped array will only store last
        $hourly1 = $this->createPriceType('hourly');
        $hourly2 = $this->createPriceType('hourly');
        $collection = new PriceTypeCollection([$hourly1, $hourly2]);

        // Both objects exist in types
        $this->assertCount(2, $collection);
        // But "has" should still return true for 'hourly'
        $this->assertTrue($collection->has('hourly'));
    }

    public function testNames(): void
    {
        $type = $this->createPriceType('hourly');
        $monthly = $this->createPriceType('monthly');

        $collection = new PriceTypeCollection([$type, $monthly]);

        $this->assertSame(['hourly', 'monthly'], $collection->names());
    }

    public function testThrowsExceptionWhenInvalidItemProvided(): void
    {
        $invalidItem = new \stdClass(); // not a PriceTypeInterface instance

        $this->expectException(InvalidPriceTypeCollectionException::class);
        $this->expectExceptionMessage('PriceTypeCollection can only contain instances of PriceTypeInterface. Got: stdClas');

        new PriceTypeCollection([$invalidItem]);
    }
}
