<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

class MockTariffType extends TariffType
{
    public function name(): string
    {
        return 'mock_tariff_type';
    }

    public function label(): string
    {
        return 'Mock Tariff Type';
    }
}
