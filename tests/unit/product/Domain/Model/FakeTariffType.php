<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

class FakeTariffType extends TariffType
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
