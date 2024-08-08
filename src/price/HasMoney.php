<?php

namespace hiqdev\php\billing\price;

use Money\Currency;
use Money\Money;

trait HasMoney
{
    protected Money $price;

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getCurrency(): Currency
    {
        return $this->price->getCurrency();
    }
}