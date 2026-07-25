<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

class DummyTariffType extends TariffType
{
    public function name(): string
    {
        return 'dummy';
    }

    public function label(): string
    {
        return 'Dummy';
    }
}
