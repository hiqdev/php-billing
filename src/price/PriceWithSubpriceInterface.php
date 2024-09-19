<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\billing\hiapi\price\SubPrices;

interface PriceWithSubpriceInterface
{
    public function getSubprices(): SubPrices;

    public function getSubprice(string $currencyCode);
}
