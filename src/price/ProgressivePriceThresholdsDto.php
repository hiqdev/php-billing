<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use Money\Currency;

class ProgressivePriceThresholdsDto
{
   public float $price;

   public Currency $currency;

   public float $value;

   public function __construct(float $price, Currency $currency, float $value)
   {
       $this->price = $price;
       $this->currency = $currency;
       $this->value = $value;
   }
}
