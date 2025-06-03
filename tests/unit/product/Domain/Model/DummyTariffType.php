<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

class DummyTariffType extends TariffType
{
    public string $name = 'dummy';

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return 'Dummy';
    }
}
