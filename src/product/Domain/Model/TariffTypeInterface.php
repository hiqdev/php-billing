<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model;

interface TariffTypeInterface
{
    public function name(): string;

    public function label(): string;
}
