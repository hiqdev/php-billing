<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\billing\hiapi\price\SubPrices;

interface PricedWithSubpriceInterface
{
    public function getSubprices(): SubPrices;

    public function getSubprice(string $currencyCode);
}
