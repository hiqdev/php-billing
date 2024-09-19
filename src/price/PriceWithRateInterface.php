<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

interface PriceWithRateInterface
{
    public function getRate(): float;
}
