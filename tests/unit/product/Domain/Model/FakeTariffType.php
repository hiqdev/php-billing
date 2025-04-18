<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

class FakeTariffType implements TariffTypeInterface
{
    public function name(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Fake';
    }
}
