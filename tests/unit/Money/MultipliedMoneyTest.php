<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\Money;

use Generator;
use hiqdev\php\billing\Money\MultipliedMoney;
use PHPUnit\Framework\TestCase;

/**
 * Class MultipliedMoneyTest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class MultipliedMoneyTest extends TestCase
{
    /**
     * @param numeric-string $amountCents
     * @dataProvider moneyParsingProvider
     */
    public function testMultipliedMoneyParsing(string $amountCents, string $expectedCents, int $expectedMultiplier): void
    {
        $currencyCode = 'USD';
        $multipliedMoney = MultipliedMoney::create($amountCents, $currencyCode);
        $this->assertSame($currencyCode, $multipliedMoney->getCurrency()->getCode());
        $this->assertSame($expectedCents, $multipliedMoney->getAmount());
        $this->assertSame($expectedMultiplier, $multipliedMoney->multiplier());
    }

    public function moneyParsingProvider(): Generator
    {
        yield ['0', '0', 1];
        yield ['1', '100', 1];
        yield ['103', '10300', 1];
        yield ['1.32', '13200', 100];
        yield ['0.01', '100', 100];
        yield ['0.001', '100', 1000];
        yield ['0.0001', '100', 10000];
        yield ['2.1', '2100', 10];
        yield ['-2.234', '-223400', 1000];
    }
}
