<?php

declare(strict_types=1);

namespace hiqdev\php\billing\bill;

class BillReversesId
{
    /** @var int|null */
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }
}
