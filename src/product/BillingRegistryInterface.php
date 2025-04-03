<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use Generator;
use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\trait\HasLockInterface;

interface BillingRegistryInterface extends HasLockInterface
{
    /**
     * @return Generator<PriceTypeDefinition>
     */
    public function priceTypes(): Generator;
}
