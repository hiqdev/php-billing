<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

interface PriceWithSumsInterface
{
    public function getSums(): Sums;
}
