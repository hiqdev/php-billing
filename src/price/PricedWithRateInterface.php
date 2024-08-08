<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

interface PricedWithRateInterface
{
    public function getRate(): float;
}
