<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface ProductInterface
{
    public function toProductName(): string;

    public function label(): string;
}