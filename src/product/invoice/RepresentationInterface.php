<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hiqdev\php\billing\type\TypeInterface;

interface RepresentationInterface
{
    public function getSql(): string;

    public function getType(): TypeInterface;

    public function setType(TypeInterface $type): RepresentationInterface;
}
