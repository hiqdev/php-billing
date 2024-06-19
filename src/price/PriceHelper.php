<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Currency;
use Money\Money;

class PriceHelper
{
    public static function buildMoney(string $price, string $currency): ?Money
    {
        if (!is_numeric($price)) {
            throw new \InvalidArgumentException('price of the Money must be numeric');
        }
        if (is_int($price)) {
            return new Money($price, new Currency($currency));
        }
        if (!is_int($price) && ($price * 100) == ((int) ($price * 100))) {
            return new Money((int) $price * 100, new Currency($currency));
        }
        list($int, $float) = explode('.', $price);
        return new Money(
            (int) ($price * (int) (1 . implode(array_fill(0, strlen($float),0)))),
            new Currency($currency)
        );
    }

    public static function buildQuantityByMoneyPrice(string $price, string $unit, string $quantity): ?Quantity
    {
        if (!is_numeric($price)) {
            throw new \InvalidArgumentException('price of the Money must be numeric');
        }
        if (is_int($price)) {
            return Quantity::create(Unit::create($unit), $quantity);
        }
        if (!is_int((int) $price) && ($price * 100) == ((int) ($price * 100))) {
            return Quantity::create(Unit::create($unit), $quantity * 100);
        }
        list($int, $float) = explode('.', $price);
        return Quantity::create(
            Unit::create($unit),
            $quantity * (int) (1 . implode(array_fill(0, strlen($float),0)))
        );
    }

}
