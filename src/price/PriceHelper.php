<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class PriceHelper
{
    public static function buildMoney(string $price, string $currency): ?Money
    {
        if (!is_numeric($price)) {
            throw new \InvalidArgumentException('price of the Money must be numeric');
        }
        if (!self::checkFloat($price)) {
            return new Money($price, new Currency($currency));
        }
        if (!is_int($price) && ($price * 100) == ((int) ($price * 100))) {
            return (new DecimalMoneyParser(new ISOCurrencies()))->parse($price, new Currency($currency));
        }
        return new Money(
            (int) ($price * self::calculatePriceRate($price)),
            new Currency($currency)
        );
    }

    public static function calculatePriceRate(string $price): int
    {
        $price = self::divide($price);
        if (!is_array($price)) {
            return 1;
        }
        return (int) (1 . implode(array_fill(0, strlen($price[1]),0)));
    }

    private static function divide(string $number): array | string
    {
        if (self::checkFloat($number)) {
            return explode('.', $number);
        }
        return $number;
    }

    private static function checkFloat(string $number): bool
    {
        return strpos($number, '.') > 0;
    }
}
