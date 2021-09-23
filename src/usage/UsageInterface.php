<?php
declare(strict_types=1);

namespace hiqdev\php\billing\usage;

use DateTimeImmutable;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;

interface UsageInterface
{
    public function target(): TargetInterface;

    public function time(): DateTimeImmutable;

    public function type(): TypeInterface;

    public function amount(): Quantity;
}
