<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface BillingRegistryInterface
{
    public function priceTypes(): \Generator;
}