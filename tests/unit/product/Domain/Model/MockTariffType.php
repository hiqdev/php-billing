<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\Domain\Model;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

class MockTariffType implements TariffTypeInterface
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
