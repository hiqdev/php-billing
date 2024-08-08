<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use Money\Money;

interface PriceWithMoneyInterface extends PriceWithCurrencyInterface
{
    public function getPrice(): Money;
}
