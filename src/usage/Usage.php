<?php

declare(strict_types=1);

namespace hiqdev\php\billing\usage;

use DateTimeImmutable;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;

/**
 * Class Usage represents a consumption of some metered resource $type at $time
 * by the $target with the given $amount.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Usage implements UsageInterface
{
    public function __construct(
        private readonly TargetInterface $target,
        private readonly DateTimeImmutable $time,
        private readonly TypeInterface $type,
        private readonly Quantity $amount
    ) {
    }

    public function target(): TargetInterface
    {
        return $this->target;
    }

    public function time(): DateTimeImmutable
    {
        return $this->time;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }

    public function amount(): Quantity
    {
        return $this->amount;
    }
}
