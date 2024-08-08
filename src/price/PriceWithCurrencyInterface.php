<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use Money\Currency;

interface PriceWithCurrencyInterface
{
    public function getCurrency(): Currency;
}
