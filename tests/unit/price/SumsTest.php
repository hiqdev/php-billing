<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\price;

use hiqdev\php\billing\price\PriceInvalidArgumentException;
use hiqdev\php\billing\price\Sums;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class SumsTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $validSums = [1 => 10.5, 2 => 15.0, 3 => 20];

        $sums = new Sums($validSums);

        $this->assertSame($validSums, $sums->values());
    }

    public function testConstructorWithInvalidDataThrowsException(): void
    {
        $this->expectException(PriceInvalidArgumentException::class);
        $this->expectExceptionMessage('All sums must be numeric values.');

        $invalidSums = [1 => 10, 2 => 'abc'];

        new Sums($invalidSums);
    }

    public function testGetSum(): void
    {
        $sums = new Sums([1 => 10, 2 => 15, 3 => 20]);

        $this->assertSame(10, $sums->getSum(1));
        $this->assertSame(15, $sums->getSum(2));
        $this->assertNull($sums->getSum(4));  // Not found
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('minSumDataProvider')]
    public function testGetMinSum($input, $expected): void
    {
        $sums = new Sums($input);

        $this->assertSame($expected, $sums->getMinSum());
    }

    public static function minSumDataProvider(): array
    {
        return [
            // Single element case
            'single positive integer' => [[1 => 10], 10],
            'single negative integer' => [[1 => -10], -10],
            'single decimal' => [[1 => 5.5], 5.5],

            // Multiple elements case
            'multiple positive integers' => [[1 => 10, 2 => 20, 3 => 5], 5],
            'multiple with negative integers' => [[1 => -10, 2 => 20, 3 => 0], -10],
            'multiple decimals' => [[1 => 10.5, 2 => 20.7, 3 => 5.5], 5.5],
            'mixed integers and decimals' => [[1 => 10, 2 => 20.7, 3 => 5], 5],

            // Edge cases
            'positive and negative decimals' => [[1 => -1.5, 2 => 1.5], -1.5],
            'zero and positive integers' => [[1 => 0, 2 => 5], 0],
            'zero and negative integers' => [[1 => 0, 2 => -5], -5],
        ];
    }

    public function testJsonSerializableBehavior(): void
    {
        $sums = new Sums([1 => 10, 2 => 15, 3 => 20]);

        $this->assertInstanceOf(JsonSerializable::class, $sums);

        $expectedJson = json_encode([1 => 10, 2 => 15, 3 => 20]);
        $this->assertSame($expectedJson, json_encode($sums));
    }

    public function testSumsWithNullInput(): void
    {
        $sums = new Sums(null);

        $this->assertNull($sums->values());
        $this->assertNull($sums->getSum(1));
    }

    public function testSumsWithEmptyArray(): void
    {
        $sums = new Sums([]);

        $this->assertSame([], $sums->values());
        $this->assertNull($sums->getSum(1));
    }
}