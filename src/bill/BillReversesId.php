<?php

declare(strict_types=1);

namespace hiqdev\php\billing\bill;

readonly class BillReversesId
{
    public function __construct(private ?int $value = null)
    {
    }

    public function getId(): ?int
    {
        return $this->value;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }
}
