<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

class DummyTariffType implements TariffTypeInterface {
    public function name(): string
    {
        return 'dummy';
    }

    public function label(): string
    {
        return 'Dummy';
    }
}
