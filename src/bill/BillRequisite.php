<?php

declare(strict_types=1);

namespace hiqdev\php\billing\bill;

class BillRequisite
{
    /**
     * @param int|string|null $id
     */
    public function __construct(
        protected $id = null,
        protected ?string $name = null
    ) {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
